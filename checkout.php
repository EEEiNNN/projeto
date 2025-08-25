<?php
include 'header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

$stmtCart = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmtCart->execute([$user_id]);
$carrinho = $stmtCart->fetch();
$carrinho_id = $carrinho ? $carrinho['id'] : 0;

$stmtItens = $pdo->prepare("
    SELECT ic.quantidade, p.nome, p.preco, (ic.quantidade * p.preco) as subtotal
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

$total = array_sum(array_column($itens_carrinho, 'subtotal'));

$stmtAddr = $pdo->prepare("
    SELECT e.* FROM endereco e 
    LEFT JOIN usuarios u ON u.endereco_id = e.id 
    WHERE u.id = ?
");
$stmtAddr->execute([$user_id]);
$endereco = $stmtAddr->fetch(PDO::FETCH_ASSOC);
?>

<title>Finalizar Compra | Ben-David</title>
<link rel="stylesheet" href="_css/checkout.css">

<main>
    <section class="checkout-section">
        <div class="checkout-container">
            <h1 class="checkout-header">Finalizar Compra</h1>

            <?php if (isset($_SESSION['feedback_checkout'])): ?>
                <div class="feedback-error">
                    <?php echo htmlspecialchars($_SESSION['feedback_checkout']); unset($_SESSION['feedback_checkout']); ?>
                </div>
            <?php endif; ?>

            <div class="checkout-content">
                <div class="customer-details">
                    <h2>Endereço de Entrega</h2>
                    <form id="checkout-form" action="finalizar_pedido.php" method="POST">
                        <input type="hidden" name="endereco_id" value="<?php echo $endereco['id'] ?? ''; ?>">
                        <div class="form-group">
                            <label>Nome do Destinatário</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars($_SESSION['nome']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($endereco['cep'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Rua</label>
                            <input type="text" id="rua" name="rua" value="<?php echo htmlspecialchars($endereco['rua'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($endereco['numero'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($endereco['bairro'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($endereco['cidade'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Estado (UF)</label>
                            <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($endereco['estado'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Complemento (Opcional)</label>
                            <input type="text" name="complemento" value="<?php echo htmlspecialchars($endereco['complemento'] ?? ''); ?>">
                        </div>
                    </form>
                </div>

                <div class="order-summary">
                    <h2>Resumo do Pedido</h2>
                    <?php foreach ($itens_carrinho as $item): ?>
                        <div class="summary-item">
                            <span><?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['nome']); ?></span>
                            <span>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="summary-total">
                        <strong>Total</strong>
                        <strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong>
                    </div>
                    <button type="submit" form="checkout-form" class="btn-finalizar">Finalizar e Pagar</button>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="_js/getCEP.js"></script>
<?php include 'footer.php'; ?>