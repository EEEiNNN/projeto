<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

// Validação dos dados de entrada (removido 'payment')
$required_fields = ['user_id', 'total', 'address'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => "Campo obrigatório ausente: $field"]);
        exit();
    }
}

$userId = $input['user_id'];
$total = $input['total'];
$address = $input['address'];

// Inicia a transação para garantir a integridade dos dados
$pdo->beginTransaction();

try {
    // 1. Inserir o endereço
    $sql = "INSERT INTO endereco (cep, rua, numero, complemento, bairro, estado, cidade, usuario_id) VALUES (:cep, :rua, :numero, :complemento, :bairro, :estado, :cidade, :usuario_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':cep' => $address['cep'],
        ':rua' => $address['rua'],
        ':numero' => $address['numero'],
        ':complemento' => $address['complemento'],
        ':bairro' => $address['bairro'],
        ':estado' => $address['estado'],
        ':cidade' => $address['cidade'],
        ':usuario_id' => $userId
    ]);
    $enderecoId = $pdo->lastInsertId();

    // 2. Buscar o carrinho do usuário
    $stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $cart = $stmt->fetch();
    if (!$cart) {
        throw new Exception("Carrinho não encontrado.");
    }
    $cartId = $cart['id'];

    // 3. Criar o registro de pagamento
    $sql = "INSERT INTO pagamento (metodo, status, valor, data_pagamento, carrinho_id) VALUES (:metodo, :status, :valor, NOW(), :carrinho_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':metodo' => 'pix', // Método de pagamento alterado para PIX
        ':status' => 'pendente', // Status inicial para PIX
        ':valor' => $total,
        ':carrinho_id' => $cartId
    ]);
    $pagamentoId = $pdo->lastInsertId();
    
    // Etapa 4 (Cartão de Crédito) foi removida.

    // 5. Criar o pedido
    $sql = "INSERT INTO pedidos (status, total, data_pedidos, usuario_id, endereco_id, carrinho_id) VALUES ('aguardando pagamento', :total, NOW(), :usuario_id, :endereco_id, :carrinho_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':total' => $total,
        ':usuario_id' => $userId,
        ':endereco_id' => $enderecoId,
        ':carrinho_id' => $cartId
    ]);
    $pedidoId = $pdo->lastInsertId();

    // 6. Mover itens do carrinho para itens do pedido
    $stmt = $pdo->prepare("SELECT produto_id, quantidade, (SELECT preco FROM produto WHERE id = produto_id) as preco FROM itemcarrinho WHERE carrinho_id = :cart_id");
    $stmt->execute(['cart_id' => $cartId]);
    $items = $stmt->fetchAll();

    foreach ($items as $item) {
        $sql = "INSERT INTO itempedidos (preco, quantidade, produto_id, pedidos_id) VALUES (:preco, :quantidade, :produto_id, :pedidos_id)";
        $stmtItem = $pdo->prepare($sql);
        $stmtItem->execute([
            ':preco' => $item['preco'],
            ':quantidade' => $item['quantidade'],
            ':produto_id' => $item['produto_id'],
            ':pedidos_id' => $pedidoId
        ]);
    }

    // 7. Limpar o carrinho
    $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = :cart_id");
    $stmt->execute(['cart_id' => $cartId]);

    // Se tudo deu certo, confirma a transação
    $pdo->commit();

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Pedido finalizado com sucesso! Aguardando pagamento via PIX.', 'pedido_id' => $pedidoId]);

} catch (Exception $e) {
    // Se algo deu errado, desfaz tudo
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro ao finalizar pedido: ' . $e->getMessage()]);
}
?>

