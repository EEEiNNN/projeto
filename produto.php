<?php
include 'header.php';
require_once 'models/Produto.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$produtoModel = new Produto($pdo);
$produto = $produtoModel->findById($id);

if (!$produto) {
    echo "<div class='container' style='max-width: 1200px;
  margin: 0 auto;
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  display: flex;
  gap: 400px;
  padding: 400px;'><h1>Produto não encontrado.</h1></div>";
    exit;
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($produto['nome']) ?> | Ben-David</title>
  <link rel="stylesheet" href="_css/produto.css">
</head>
<body>
  <div class="container">
    <div class="produto">
      <div class="imagem">
        <img src="<?= $produtoModel->getImagemPrincipal($produto['id']) ?? 'imagens/padrao.jpg' ?>" 
             alt="<?= htmlspecialchars($produto['nome']) ?>">
      </div>
      <div class="detalhes">
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        <p class="categoria"><?= htmlspecialchars($produto['categoria'] ?? 'Sem categoria') ?></p>
        <p class="descricao"><?= nl2br(htmlspecialchars($produto['descricao'] ?? 'Sem descrição disponível.')) ?></p>
        <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        <p class="estoque">
          <?= ($produto['estoque'] > 0) ? 'Em estoque' : 'Indisponível' ?>
        </p>

        <?php if ($produto['estoque'] > 0): ?>
          <button class="comprar btn-add-carrinho" data-produto-id="<?= $produto['id'] ?>">
            Adicionar ao carrinho
          </button>
        <?php else: ?>
          <button class="comprar" disabled>Indisponível</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
<script src="js/shop_events.js"></script>
<?php include 'footer.php'; ?>

