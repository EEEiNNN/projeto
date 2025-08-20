<?php
include 'conexao.php';
require_once 'models/Produto.php';

// Recupera o ID da URL
$id = $_GET['id'] ?? null;

// Se não tiver id, redireciona para página inicial
if (!$id) {
    header("Location: index.php");
    exit;
}

// Busca produto pelo Model
$produtoModel = new Produto($pdo);
$produto = $produtoModel->findById($id);

// Se não encontrar o produto
if (!$produto) {
    include 'header.php';
    echo "<div class='container'><h1>Produto não encontrado.</h1></div>";
    include 'footer.php';
    exit;
}

include 'header.php';
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
          <button class="comprar">Adicionar ao carrinho</button>
        <?php else: ?>
          <button class="comprar" disabled>Indisponível</button>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" 
          crossorigin="anonymous"></script>
  <?php include 'footer.php'; ?>
</body>
</html>
