<?php
require_once("../../../conexao.php");

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo "ID não recebido";
    exit();
}

try {
    // Inicia uma transação para garantir a integridade dos dados
    $pdo->beginTransaction();

    // Passo 1: Apagar registos dependentes na tabela 'endereco'
    $stmtEnd = $pdo->prepare("DELETE FROM endereco WHERE usuario_id = :id");
    $stmtEnd->bindValue(":id", $id);
    $stmtEnd->execute();

    // Adicione aqui a exclusão de outras tabelas que possam ter o 'usuario_id'
    // Exemplo para 'pedidos' e 'carrinho' (IMPORTANTE: apague os itens primeiro)

    // Apagar itens do carrinho
    $stmtItensCarrinho = $pdo->prepare("DELETE ic FROM itemcarrinho ic JOIN carrinho c ON ic.carrinho_id = c.id WHERE c.usuario_id = :id");
    $stmtItensCarrinho->bindValue(":id", $id);
    $stmtItensCarrinho->execute();
    
    // Apagar carrinho
    $stmtCarrinho = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = :id");
    $stmtCarrinho->bindValue(":id", $id);
    $stmtCarrinho->execute();

    // Apagar itens dos pedidos
    $stmtItensPedidos = $pdo->prepare("DELETE ip FROM itempedidos ip JOIN pedidos p ON ip.pedidos_id = p.id WHERE p.usuario_id = :id");
    $stmtItensPedidos->bindValue(":id", $id);
    $stmtItensPedidos->execute();

    // Apagar pedidos
    $stmtPedidos = $pdo->prepare("DELETE FROM pedidos WHERE usuario_id = :id");
    $stmtPedidos->bindValue(":id", $id);
    $stmtPedidos->execute();


    // Passo 2: Agora, apagar o utilizador principal
    $stmtUser = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmtUser->bindValue(":id", $id);
    $stmtUser->execute();

    // Se tudo correu bem, confirma as alterações na base de dados
    $pdo->commit();
    echo "Excluído com Sucesso";

} catch (Exception $e) {
    // Se algo deu errado, desfaz todas as alterações
    $pdo->rollBack();
    echo "Erro ao excluir: " . $e->getMessage();
}
?>