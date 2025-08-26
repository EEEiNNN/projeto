<?php
require_once("../../../conexao.php");

$id = $_POST['id'] ?? '';
$usuario_id = $_POST['usuario_id'];
$status = $_POST['status'];
$endereco = $_POST['endereco'];
$itens = $_POST['produtos'] ?? [];
$total_pedido = 0;

if (empty($usuario_id) || empty($itens)) {
    echo "Cliente e pelo menos um item são obrigatórios!";
    exit();
}

// Calcula o total do pedido
foreach ($itens as $item) {
    $total_pedido += (float)$item['preco_unitario'] * (int)$item['quantidade'];
}

try {
    $pdo->beginTransaction();

    if (empty($id)) { // --- INSERIR NOVO PEDIDO ---
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, status, total, data_pedidos, endereco) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute([$usuario_id, $status, $total_pedido, $endereco]);
        $pedido_id = $pdo->lastInsertId();
    } else { // --- ATUALIZAR PEDIDO EXISTENTE ---
        $pedido_id = $id;
        $stmt = $pdo->prepare("UPDATE pedidos SET usuario_id = ?, status = ?, total = ?, endereco = ? WHERE id = ?");
        $stmt->execute([$usuario_id, $status, $total_pedido, $endereco, $pedido_id]);

        // Apaga os itens antigos para reinserir os novos (maneira mais simples de atualizar)
        $stmtDel = $pdo->prepare("DELETE FROM itempedidos WHERE pedidos_id = ?");
        $stmtDel->execute([$pedido_id]);
    }

    // Insere os itens do pedido
    $stmtItem = $pdo->prepare("INSERT INTO itempedidos (pedidos_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)");
    foreach ($itens as $item) {
        $stmtItem->execute([
            $pedido_id,
            $item['produto_id'],
            $item['quantidade'],
            $item['preco_unitario']
        ]);
    }

    $pdo->commit();
    echo "Salvo com Sucesso";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Ocorreu um erro ao salvar o pedido: " . $e->getMessage();
}
?>