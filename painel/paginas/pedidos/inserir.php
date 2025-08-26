<?php
require_once("../../../conexao.php");

// PASSO 1: Capturar e Validar os Dados do Formulário
$id = $_POST['id'] ?? '';
$usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? 'pendente';
$endereco_id = filter_input(INPUT_POST, 'endereco_id', FILTER_VALIDATE_INT);
$itens = $_POST['produtos'] ?? [];

// Validação básica
if (!$usuario_id || !$endereco_id || empty($itens)) {
    echo "Cliente, Endereço e pelo menos um Item são obrigatórios!";
    exit();
}

try {
    // PASSO 2: Iniciar a Transação de Banco de Dados
    // Isto garante que ou todas as operações são bem-sucedidas, ou nenhuma é salva.
    $pdo->beginTransaction();

    // PASSO 3: Calcular o Total no Servidor (para Segurança)
    $total_pedido = 0;
    $stmtPreco = $pdo->prepare("SELECT preco FROM produto WHERE id = ?");
    foreach ($itens as $item) {
        $stmtPreco->execute([$item['produto_id']]);
        $produto = $stmtPreco->fetch();
        if ($produto) {
            $total_pedido += (float)$produto['preco'] * (int)$item['quantidade'];
        }
    }

    // PASSO 4: Inserir ou Atualizar o Pedido Principal
    if (empty($id)) {
        // --- INSERIR UM NOVO PEDIDO ---
        $stmtPedido = $pdo->prepare(
            "INSERT INTO pedidos (usuario_id, status, total, data_pedidos, endereco_id) 
             VALUES (?, ?, ?, NOW(), ?)"
        );
        $stmtPedido->execute([$usuario_id, $status, $total_pedido, $endereco_id]);
        $pedido_id = $pdo->lastInsertId(); // Pega o ID do novo pedido

    } else {
        // --- ATUALIZAR UM PEDIDO EXISTENTE ---
        $pedido_id = $id;
        $stmtPedido = $pdo->prepare(
            "UPDATE pedidos SET usuario_id = ?, status = ?, total = ?, endereco_id = ? WHERE id = ?"
        );
        $stmtPedido->execute([$usuario_id, $status, $total_pedido, $endereco_id, $pedido_id]);

        // Apaga os itens antigos para reinserir a lista atualizada
        $stmtDel = $pdo->prepare("DELETE FROM itempedidos WHERE pedidos_id = ?");
        $stmtDel->execute([$pedido_id]);
    }

    // PASSO 5: Inserir os Itens do Pedido
    $stmtItem = $pdo->prepare(
        "INSERT INTO itempedidos (pedidos_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)"
    );
    foreach ($itens as $item) {
        // Busca o preço atual do produto para garantir consistência
        $stmtPreco->execute([$item['produto_id']]);
        $produto = $stmtPreco->fetch();
        $preco_atual = $produto['preco'] ?? 0;
        
        $stmtItem->execute([
            $pedido_id,
            $item['produto_id'],
            $item['quantidade'],
            $preco_atual
        ]);
    }

    // PASSO 6: Confirmar a Transação
    // Se todas as operações acima foram bem-sucedidas, salva permanentemente.
    $pdo->commit();
    echo "Salvo com Sucesso";

} catch (Exception $e) {
    // PASSO 7: Reverter a Transação em Caso de Erro
    // Se qualquer operação falhou, desfaz tudo o que foi feito.
    $pdo->rollBack();
    echo "Ocorreu um erro ao salvar o pedido: " . $e->getMessage();
}
?>