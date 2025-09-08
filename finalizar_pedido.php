<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

// Verifica se o usuário está logado e o método é POST
if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$endereco_id = $_POST['endereco_id'] ?? null;

// Dados do endereço
$cep = trim($_POST['cep']);
$rua = trim($_POST['rua']);
$numero = trim($_POST['numero']);
$complemento = trim($_POST['complemento']);
$bairro = trim($_POST['bairro']);
$cidade = trim($_POST['cidade']);
$estado = trim($_POST['estado']);

// Busca o carrinho e os itens
$stmtCart = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmtCart->execute([$user_id]);
$carrinho = $stmtCart->fetch();
$carrinho_id = $carrinho ? $carrinho['id'] : 0;

$stmtItens = $pdo->prepare("
    SELECT ic.produto_id, ic.quantidade, p.preco 
    FROM itemcarrinho ic 
    JOIN produto p ON ic.produto_id = p.id 
    WHERE ic.carrinho_id = ?
");
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

        // $stmtUser = $pdo->prepare("UPDATE usuarios SET endereco_id = ? WHERE id = ?");
        // $stmtUser->execute([$endereco_id, $user_id]);
    }

    if (!$endereco_id) {
        throw new Exception("O endereço de entrega é obrigatório.");
    }
    
    $total_final = 0;
    foreach($itens_carrinho as $item) {
        $total_final += $item['preco'] * $item['quantidade'];
    }
   
    $_SESSION['pedido_pendente'] = [
        'usuario_id' => $user_id,
        'endereco_id' => $endereco_id,
        'carrinho_id' => $carrinho_id,
        'total' => $total_final,
        'itens' => $itens_carrinho
    ];
    
    $pdo->commit(); 
    
    header('Location: pix.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // Para depuração, é útil ver o erro real. Pode mudar a linha abaixo temporariamente.
    // $_SESSION['feedback_checkout'] = "Erro: " . $e->getMessage();
    $_SESSION['feedback_checkout'] = "Ocorreu um erro ao preparar seu pedido. Por favor, tente novamente.";
    header('Location: checkout.php');
    exit;
}
?>