<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

// Verifica se o usuário está logado e se existe um pedido pendente
if (!isLoggedIn() || !isset($_SESSION['pedido_pendente'])) {
    header('Location: login.php');
    exit;
}

// Pega os dados da sessão
$pedido_data = $_SESSION['pedido_pendente'];
$user_id = $pedido_data['usuario_id'];
$endereco_id = $pedido_data['endereco_id'];
$carrinho_id = $pedido_data['carrinho_id'];
$total_final = $pedido_data['total'];
$itens_carrinho = $pedido_data['itens'];

try {
    $pdo->beginTransaction();

    // 1. Insere o pedido na tabela `pedidos`
    // Agora o status pode ser 'pago' ou 'processando'
    $stmtPedido = $pdo->prepare(
        "INSERT INTO pedidos (usuario_id, total, data_pedidos, status, endereco_id) 
         VALUES (?, ?, NOW(), 'processando', ?)"
    );
    $stmtPedido->execute([$user_id, $total_final, $endereco_id]);
    $pedido_id = $pdo->lastInsertId();

    // 2. Move os itens para a tabela `itempedidos`
    $stmtMoverItem = $pdo->prepare(
        "INSERT INTO itempedidos (pedidos_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)"
    );
    foreach ($itens_carrinho as $item) {
        $stmtMoverItem->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $item['preco']]);
    }
    
    // 3. Limpa o carrinho de compras
    $stmtLimpar = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ?");
    $stmtLimpar->execute([$carrinho_id]);

    $pdo->commit();
    
    // Limpa a sessão para não criar o mesmo pedido duas vezes
    unset($_SESSION['pedido_pendente']);

    // Redireciona para a página de sucesso
    header('Location: meus-pedidos.php?status=sucesso');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // Se der erro, manda de volta para o carrinho com uma mensagem
    $_SESSION['feedback_carrinho'] = "Ocorreu um erro ao confirmar seu pedido. Por favor, tente novamente.";
    // Para depuração: $_SESSION['feedback_carrinho'] = "Erro: " . $e->getMessage();
    header('Location: carrinho.php');
    exit;
}
?>