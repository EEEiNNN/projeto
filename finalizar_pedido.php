<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$endereco_id = $_POST['endereco_id'] ?? null;

$cep = trim($_POST['cep']);
$rua = trim($_POST['rua']);
$numero = trim($_POST['numero']);
$complemento = trim($_POST['complemento']);
$bairro = trim($_POST['bairro']);
$cidade = trim($_POST['cidade']);
$estado = trim($_POST['estado']);

$stmtCart = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmtCart->execute([$user_id]);
$carrinho = $stmtCart->fetch();
$carrinho_id = $carrinho ? $carrinho['id'] : 0;

$stmtItens = $pdo->prepare("SELECT * FROM itemcarrinho WHERE carrinho_id = ?");
$stmtItens->execute([$carrinho_id]);
$itens_carrinho = $stmtItens->fetchAll();

if (empty($itens_carrinho)) {
    header('Location: carrinho.php');
    exit;
}

try {
    $pdo->beginTransaction();

    if ($endereco_id) {
        $stmtAddr = $pdo->prepare(
            "UPDATE endereco SET cep=?, rua=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE id=? AND usuario_id=?"
        );
        $stmtAddr->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $endereco_id, $user_id]);
    } else if (!empty($cep) && !empty($rua)) {
        $stmtAddr = $pdo->prepare(
            "INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmtAddr->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $user_id]);
        $endereco_id = $pdo->lastInsertId();

        $stmtUser = $pdo->prepare("UPDATE usuarios SET endereco_id = ? WHERE id = ?");
        $stmtUser->execute([$endereco_id, $user_id]);
    }

    if (!$endereco_id) {
        throw new Exception("O endereço de entrega é obrigatório.");
    }

    $total_final = 0;
    $stmtPreco = $pdo->prepare("SELECT preco FROM produto WHERE id = ?");
    foreach($itens_carrinho as $item) {
        $stmtPreco->execute([$item['produto_id']]);
        $produto = $stmtPreco->fetch();
        if ($produto) {
            $total_final += $produto['preco'] * $item['quantidade'];
        }
    }

    $stmtPedido = $pdo->prepare(
        "INSERT INTO pedidos (usuario_id, total, data_pedidos, status, endereco_id) 
         VALUES (?, ?, NOW(), 'pendente', ?)"
    );
    $stmtPedido->execute([$user_id, $total_final, $endereco_id]);
    $pedido_id = $pdo->lastInsertId();

    $stmtMoverItem = $pdo->prepare(
        "INSERT INTO itempedidos (pedidos_id, produto_id, quantidade, preco) 
         SELECT ?, ic.produto_id, ic.quantidade, p.preco 
         FROM itemcarrinho ic JOIN produto p ON ic.produto_id = p.id
         WHERE ic.carrinho_id = ?"
    );
    $stmtMoverItem->execute([$pedido_id, $carrinho_id]);
    
    $stmtLimpar = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ?");
    $stmtLimpar->execute([$carrinho_id]);

    $pdo->commit();
    
    header('Location: meus-pedidos.php?status=sucesso');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    
    $_SESSION['feedback_checkout'] = "Ocorreu um erro ao finalizar o seu pedido. Por favor, tente novamente.";
    // Para depuração:
    // $_SESSION['feedback_checkout'] = "Erro: " . $e->getMessage();
    
    header('Location: checkout.php');
    exit;
}
?>