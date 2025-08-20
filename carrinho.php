<?php
include 'header.php';
require_once 'conexao.php';

// Verificar se usuário está logado
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// [LÓGICA ADICIONADA] - Garantir que o usuário tenha um carrinho principal
// Primeiro, tentamos encontrar um carrinho existente para este usuário.
$stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$carrinho = $stmt->fetch();

if ($carrinho) {
    $carrinho_id = $carrinho['id'];
} else {
    // Se não houver carrinho, criamos um novo.
    $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id, data_criacao) VALUES (?, NOW())");
    $stmt->execute([$user_id]);
    $carrinho_id = $pdo->lastInsertId(); // Pegamos o ID do carrinho recém-criado
}

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'adicionar':
                $produto_id = (int)$_POST['produto_id'];
                $quantidade = (int)$_POST['quantidade'] ?: 1;

                try {
                    // Verificar se produto existe e tem estoque
                    // [CORREÇÃO] Tabela 'produto', não 'produtos'
                    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id = ? AND estoque >= ?");
                    $stmt->execute([$produto_id, $quantidade]);
                    $produto = $stmt->fetch();

                    if (!$produto) {
                        $response['success'] = false;
                        $response['message'] = 'Produto não encontrado ou sem estoque suficiente';
                    } else {
                        // Verificar se produto já está no carrinho
                        // [CORREÇÃO] Consulta na tabela 'itemcarrinho' usando o $carrinho_id
                        $stmt = $pdo->prepare("SELECT * FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
                        $stmt->execute([$carrinho_id, $produto_id]);
                        $item_carrinho = $stmt->fetch();

                        if ($item_carrinho) {
                            // Atualizar quantidade
                            $nova_quantidade = $item_carrinho['quantidade'] + $quantidade;

                            if ($nova_quantidade > $produto['estoque']) {
                                $response['success'] = false;
                                $response['message'] = 'Estoque insuficiente';
                            } else {
                                // [CORREÇÃO] UPDATE na tabela 'itemcarrinho'
                                $stmt = $pdo->prepare("UPDATE itemcarrinho SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
                                $stmt->execute([$nova_quantidade, $carrinho_id, $produto_id]);
                                $response['success'] = true;
                                $response['message'] = 'Quantidade atualizada no carrinho';
                            }
                        } else {
                            // Adicionar novo item
                            // [CORREÇÃO] INSERT na tabela 'itemcarrinho'
                            $stmt = $pdo->prepare("INSERT INTO itemcarrinho (carrinho_id, produto_id, quantidade) VALUES (?, ?, ?)");
                            $stmt->execute([$carrinho_id, $produto_id, $quantidade]);
                            $response['success'] = true;
                            $response['message'] = 'Produto adicionado ao carrinho';
                        }
                    }
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = 'Erro ao adicionar produto: ' . $e->getMessage();
                    error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
                }
                break;

            case 'remover':
                $produto_id = (int)$_POST['produto_id'];

                try {
                    // [CORREÇÃO] DELETE da tabela 'itemcarrinho'
                    $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
                    $stmt->execute([$carrinho_id, $produto_id]);
                    $response['success'] = true;
                    $response['message'] = 'Produto removido do carrinho';
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = 'Erro ao remover produto';
                }
                break;

            case 'atualizar':
                $produto_id = (int)$_POST['produto_id'];
                $quantidade = (int)$_POST['quantidade'];

                if ($quantidade <= 0) {
                    // [CORREÇÃO] Remover item da tabela 'itemcarrinho'
                    $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
                    $stmt->execute([$carrinho_id, $produto_id]);
                    $response['success'] = true;
                    $response['message'] = 'Produto removido';
                } else {
                    // [CORREÇÃO] Tabela 'produto', não 'produtos'
                    $stmt = $pdo->prepare("SELECT estoque FROM produto WHERE id = ?");
                    $stmt->execute([$produto_id]);
                    $produto = $stmt->fetch();

                    if ($produto && $quantidade <= $produto['estoque']) {
                        // [CORREÇÃO] UPDATE na tabela 'itemcarrinho'
                        $stmt = $pdo->prepare("UPDATE itemcarrinho SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
                        $stmt->execute([$quantidade, $carrinho_id, $produto_id]);
                        $response['success'] = true;
                        $response['message'] = 'Quantidade atualizada';
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Estoque insuficiente';
                    }
                }
                break;

            case 'limpar':
                try {
                    // [CORREÇÃO] DELETE da tabela 'itemcarrinho', mantendo o 'carrinho' principal
                    $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ?");
                    $stmt->execute([$carrinho_id]);
                    $response['success'] = true;
                    $response['message'] = 'Carrinho limpo';
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = 'Erro ao limpar carrinho';
                }
                break;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// [CORREÇÃO] Consulta SQL totalmente reestruturada para o banco de dados correto
$stmt = $pdo->prepare("
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
    JOIN imagemproduto img ON p.id = img.produto_id
    WHERE ic.carrinho_id = ? AND img.principal = 1
    ORDER BY ic.id DESC
");
$stmt->execute([$carrinho_id]);
$itens_carrinho = $stmt->fetchAll();

// Calcular total
$total = 0;
foreach ($itens_carrinho as $item) {
    $total += $item['subtotal'];
}
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
    <header class="header-simple">
        <nav>
            <div class="nav__logo"><a href="index.php">Ben-David</a></div>
            <ul class="nav__links" id="nav-links">
                <li class="link"><a href="index.php">Home</a></li>
                <li class="link"><a href="index.php">Sobre</a></li>
                <li class="link"><a href="produtos.php">Produtos</a></li>
                <li class="link"><a href="index.php">Coleção</a></li>
            </ul>
            <div class="nav__menu__btn" id="menu-btn">
                <span><i class="ri-menu-line"></i></span>
            </div>
        </nav>
    </header>

    <section class="carrinho-section">
        <div class="section__container">
            <h1 class="section__header">Meu Carrinho</h1>
            
            <?php if (empty($itens_carrinho)): ?>
                <div class="carrinho-vazio">
                    <i class="ri-shopping-cart-line"></i>
                    <h3>Seu carrinho está vazio</h3>
                    <p>Adicione produtos para continuar comprando</p>
                    <a href="produtos.php" class="btn">Ver Produtos</a>
                </div>
            <?php else: ?>
                <div class="carrinho-content">
                    <div class="carrinho-itens">
                        <?php foreach ($itens_carrinho as $item): ?>
                            <div class="carrinho-item" data-produto="<?php echo $item['produto_id']; ?>">
                                <div class="item-image">
                                    <img src="<?php echo htmlspecialchars($item['url_imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" />
                                </div>
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                                    <p class="item-price">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                                    <p class="item-estoque">Estoque: <?php echo $item['estoque']; ?></p>
                                </div>
                                <div class="item-controls">
                                    <div class="quantidade-control">
                                        <button class="btn-quantidade" data-action="diminuir" data-produto="<?php echo $item['produto_id']; ?>">-</button>
                                        <input type="number" class="quantidade-input" value="<?php echo $item['quantidade']; ?>" min="1" max="<?php echo $item['estoque']; ?>" data-produto="<?php echo $item['produto_id']; ?>" />
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
                                <span><?php echo $total >= 199 ? 'Grátis' : 'A calcular'; ?></span>
                            </div>
                            <div class="resumo-linha total">
                                <span>Total:</span>
                                <span id="total">R$ <?php echo number_format($total + ($total >= 199 ? 0 : 0), 2, ',', '.'); ?></span>
                            </div>
                            <div class="resumo-actions">
                                <button class="btn-secundario btn-limpar">Limpar Carrinho</button>
                                <a href="checkout.php" class="btn btn-finalizar">Finalizar Compra</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="js/main.js"></script>
    <script src="js/carrinho.js"></script>
</body>
</html>