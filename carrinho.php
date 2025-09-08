<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

$stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$carrinho = $stmt->fetch();

if ($carrinho) {
    $carrinho_id = $carrinho['id'];
} else {
    $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id, data_criacao) VALUES (?, NOW())");
    $stmt->execute([$user_id]);
    $carrinho_id = $pdo->lastInsertId();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => 'Ocorreu um erro.'];
    
    switch ($_POST['action']) {
        case 'adicionar':
            $produto_id = (int)($_POST['produto_id'] ?? 0);
            $quantidade = (int)($_POST['quantidade'] ?? 1);

            try {
                $stmt = $pdo->prepare("SELECT estoque FROM produto WHERE id = ? AND estoque >= ?");
                $stmt->execute([$produto_id, $quantidade]);
                $produto = $stmt->fetch();

                if (!$produto) {
                    $response['message'] = 'Produto não encontrado ou sem stock suficiente';
                } else {
                    $stmtItem = $pdo->prepare("SELECT quantidade FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
                    $stmtItem->execute([$carrinho_id, $produto_id]);
                    $item_existente = $stmtItem->fetch();

                    if ($item_existente) {
                        $nova_quantidade = $item_existente['quantidade'] + $quantidade;
                        if ($nova_quantidade > $produto['estoque']) {
                            $response['message'] = 'Stock insuficiente';
                        } else {
                            $stmtUpdate = $pdo->prepare("UPDATE itemcarrinho SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
                            $stmtUpdate->execute([$nova_quantidade, $carrinho_id, $produto_id]);
                            $response = ['success' => true, 'message' => 'Quantidade atualizada'];
                        }
                    } else {
                        $stmtInsert = $pdo->prepare("INSERT INTO itemcarrinho (carrinho_id, produto_id, quantidade) VALUES (?, ?, ?)");
                        $stmtInsert->execute([$carrinho_id, $produto_id, $quantidade]);
                        $response = ['success' => true, 'message' => 'Produto adicionado ao carrinho'];
                    }
                }
            } catch (PDOException $e) {
                $response['message'] = 'Erro de base de dados ao adicionar.';
            }
            break;

        case 'remover':
            $produto_id = (int)$_POST['produto_id'];
            $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
            if ($stmt->execute([$carrinho_id, $produto_id])) {
                $response = ['success' => true];
            }
            break;

        case 'atualizar':
            $produto_id = (int)$_POST['produto_id'];
            $quantidade = (int)$_POST['quantidade'];

            if ($quantidade <= 0) {
                $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
                $stmt->execute([$carrinho_id, $produto_id]);
                $response = ['success' => true];
            } else {
                $stmtStock = $pdo->prepare("SELECT estoque FROM produto WHERE id = ?");
                $stmtStock->execute([$produto_id]);
                $produto = $stmtStock->fetch();

                if ($produto && $quantidade <= $produto['estoque']) {
                    $stmt = $pdo->prepare("UPDATE itemcarrinho SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
                    $stmt->execute([$quantidade, $carrinho_id, $produto_id]);
                    $response = ['success' => true];
                } else {
                    $response['message'] = 'Stock insuficiente!';
                }
            }
            break;

        case 'limpar':
            $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ?");
            if ($stmt->execute([$carrinho_id])) {
                $response = ['success' => true];
            }
            break;
    }

    if ($response['success']) {
        $stmtSoma = $pdo->prepare("
            SELECT SUM(p.preco * ic.quantidade) as total, COUNT(ic.id) as itemCount
            FROM itemcarrinho ic
            JOIN produto p ON ic.produto_id = p.id
            WHERE ic.carrinho_id = ?
        ");
        $stmtSoma->execute([$carrinho_id]);
        $resultado = $stmtSoma->fetch();
        
        $total_carrinho = $resultado['total'] ?? 0;
        
        $response['novoTotalCarrinho'] = number_format($total_carrinho, 2, ',', '.');
        $response['freteTexto'] = $total_carrinho >= 199 ? 'Grátis' : 'A calcular';
        $response['totalFinal'] = number_format($total_carrinho, 2, ',', '.'); 
        $response['itemCount'] = $resultado['itemCount'] ?? 0;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$stmtItens = $pdo->prepare("
    SELECT 
        ic.produto_id,
        ic.quantidade,
        p.nome, 
        p.preco, 
        p.estoque,
        img.url_imagem,
        (ic.quantidade * p.preco) as subtotal
    FROM itemcarrinho ic
    JOIN produto p ON ic.produto_id = p.id
    LEFT JOIN imagemproduto img ON p.id = img.produto_id AND img.principal = 1
    WHERE ic.carrinho_id = ?
    ORDER BY ic.id DESC
");
$stmtItens->execute([$carrinho_id]);
$itens_carrinho = $stmtItens->fetchAll();

$total = 0;
foreach ($itens_carrinho as $item) {
    $total += $item['subtotal'];
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="_css/carrinho.css" />
    <title>Carrinho | Ben-David</title>
</head>
<body>
    <main>
        <section class="carrinho-section">
            <div class="section__container">
                <h1 class="section__header">Meu Carrinho</h1>
                
                <div class="carrinho-vazio" style="<?php echo empty($itens_carrinho) ? 'display: block;' : 'display: none;'; ?>">
                    <i class="ri-shopping-cart-line"></i>
                    <h3>O seu carrinho está vazio</h3>
                    <p>Adicione produtos para continuar a comprar</p>
                    <a href="produtos.php" class="btn" style="background-color: #251B18; color: #fff;">Ver Produtos</a>
                </div>

                <div class="carrinho-content" style="<?php echo empty($itens_carrinho) ? 'display: none;' : 'display: grid;'; ?>">
                    <div class="carrinho-itens">
                        <?php foreach ($itens_carrinho as $item): ?>
                            <div class="carrinho-item" data-produto="<?php echo $item['produto_id']; ?>">
                                <div class="item-image">
                                    <img src="<?php echo htmlspecialchars($item['url_imagem'] ?? '_images/padrao.jpg'); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" />
                                </div>
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                                    <p class="item-price">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                                    <p class="item-estoque">Stock: <?php echo $item['estoque']; ?></p>
                                </div>
                                <div class="item-controls">
                                    <div class="quantidade-control">
                                        <button class="btn-quantidade" data-action="diminuir" data-produto="<?php echo $item['produto_id']; ?>">-</button>
                                        <input type="number" class="quantidade-input" value="<?php echo $item['quantidade']; ?>" min="0" max="<?php echo $item['estoque']; ?>" data-produto="<?php echo $item['produto_id']; ?>" />
                                        <button class="btn-quantidade" data-action="aumentar" data-produto="<?php echo $item['produto_id']; ?>">+</button>
                                    </div>
                                    <div class="item-subtotal">
                                        <strong>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong>
                                    </div>
                                    <button class="btn-remover" data-produto="<?php echo $item['produto_id']; ?>">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="carrinho-resumo">
                        <div class="resumo-box">
                            <h3>Resumo do Pedido</h3>
                            <div class="resumo-linha">
                                <span>Subtotal:</span>
                                <span id="subtotal">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                            <div class="resumo-linha">
                                <span>Frete:</span>
                                <span id="frete"><?php echo $total >= 199 ? 'Grátis' : 'A calcular'; ?></span>
                            </div>
                            <div class="resumo-linha total">
                                <span>Total:</span>
                                <span id="total">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                            <div class="resumo-actions">
                                <button class="btn-secundario btn-limpar">Limpar Carrinho</button>
                                <a href="checkout.php" class="btn btn-finalizar">Finalizar Compra</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="js/carrinho.js"></script> 
    <?php include 'footer.php'; ?>