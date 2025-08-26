<?php
require_once("../../../conexao.php");
header('Content-Type: application/json');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$response = ['success' => false, 'message' => 'ID do pedido inválido.'];

if ($id) {
    try {
        // Busca dados do pedido principal
        $stmtPedido = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmtPedido->execute([$id]);
        $pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            // Busca os itens do pedido
            $stmtItens = $pdo->prepare("SELECT * FROM itempedidos WHERE pedidos_id = ?");
            $stmtItens->execute([$id]);
            $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
            
            $pedido['itens'] = $itens;

            $response['success'] = true;
            $response['data'] = $pedido;
        } else {
            $response['message'] = 'Pedido não encontrado.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Erro no servidor: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>