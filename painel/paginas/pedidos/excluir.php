<?php
require_once("../../../conexao.php");
$id = $_POST['id'];

try {
    $pdo->beginTransaction();
    // Exclui primeiro os itens do pedido (chave estrangeira)
    $stmtItens = $pdo->prepare("DELETE FROM itempedido WHERE pedido_id = ?");
    $stmtItens->execute([$id]);

    // Depois exclui o pedido principal
    $stmtPedido = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmtPedido->execute([$id]);

    $pdo->commit();
    echo "Excluído com Sucesso";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Ocorreu um erro ao excluir o pedido: " . $e->getMessage();
}
?>