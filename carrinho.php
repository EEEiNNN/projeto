<?php
// [CORREÇÃO] Iniciar a sessão de forma segura no topo do ficheiro
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';

// Verificar se o utilizador está logado usando a função agora definida
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Garante que o utilizador tenha um carrinho principal
$stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$carrinho = $stmt->fetch();

if ($carrinho) {
    $carrinho_id = $carrinho['id'];
} else {
    // Se não houver carrinho, cria um novo.
    $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id, data_criacao) VALUES (?, NOW())");
    $stmt->execute([$user_id]);
    $carrinho_id = $pdo->lastInsertId();
}

// Processar ações do carrinho (AJAX)
// A sua lógica aqui já estava muito boa! Nenhuma alteração foi necessária.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = [];

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'adicionar':
                $produto_id = (int)$_POST['produto_id'];
                $quantidade = (int)($_POST['quantidade'] ?? 1);

                try {
                    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id = ? AND estoque >= ?");
                    $stmt->execute([$produto_id, $quantidade]);
                    $produto = $stmt->fetch();

                    if (!$produto) {
                        $response['success'] = false;
                        $response['message'] = 'Produto não encontrado ou sem stock suficiente';
                    } else {
                        $stmt = $pdo->prepare("SELECT * FROM itemcarrinho WHERE carrinho_id = ? AND produto_id = ?");
                        $stmt->execute([$carrinho_id, $produto_id]);
                        $item_carrinho = $stmt->fetch();

                        if ($item_carrinho) {
                            $nova_quantidade = $item_carrinho['quantidade'] + $quantidade;
                            if ($nova_quantidade > $produto['estoque']) {
                                $response['success'] = false;
                                $response['message'] = 'Stock insuficiente';
                            } else {
                                $stmt = $pdo->prepare("UPDATE itemcarrinho SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
                                $stmt->execute([$nova_quantidade, $carrinho_id, $produto_id]);
                                $response['success'] = true;
                                $response['message'] = 'Quantidade atualizada no carrinho';
                            }
                        } else {
                            $stmt = $pdo->prepare("INSERT INTO itemcarrinho (carrinho_id, produto_id, quantidade) VALUES (?, ?, ?)");
                            $stmt->execute([$carrinho_id, $produto_id, $quantidade]);
                            $response['success'] = true;
                            $response['message'] = 'Produto adicionado ao carrinho';
                        }
                    }
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = 'Erro ao adicionar produto.';
                }
                break;

            // Os outros casos (remover, atualizar, limpar) também estavam corretos.
            // ...
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Buscar itens do carrinho para exibir na página
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

// [CORREÇÃO] A inclusão do header.php vem ANTES de qualquer conteúdo HTML.

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
    <section class="carrinho-section">
        <div class="section__container">
            <h1 class="section__header">Meu Carrinho</h1>
            
            <?php if (empty($itens_carrinho)): ?>
                <div class="carrinho-vazio">
                    <i class="ri-shopping-cart-line"></i>
                    <h3>O seu carrinho está vazio</h3>
                    <p>Adicione produtos para continuar a comprar</p>
                    <a href="produtos.php" class="btn" style="background-color: #251B18; color: #fff;">Ver Produtos</a>
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
<script src="js/main.js"></script> 
<script src="js/carrinho.js"></script>
<?php include 'footer.php'; ?>