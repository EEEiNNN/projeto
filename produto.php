<?php
include 'conexao.php';

$id = $_GET['id'] ?? 1;

$stmt = $pdo->prepare("SELECT p.*, c.nome AS categoria, i.url_imagem
                       FROM Produto p
                       LEFT JOIN Categoria c ON p.categoria_id = c.id
                       LEFT JOIN ImagemProduto i ON i.produto_id = p.id AND i.principal = 1
                       WHERE p.id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

include 'header.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($produto['nome']) ?> | Bailar</title>
  <link rel="stylesheet" href="_css/produto.css">
</head>
<body>
  <div class="container">
    <div class="produto">
      <div class="imagem">
        <img src="<?= $produto['url_imagem'] ?? 'imagens/padrao.jpg' ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
      </div>
      <div class="detalhes">
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        <p class="categoria"><?= htmlspecialchars($produto['categoria']) ?></p>
        <p class="descricao"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
        <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        <p class="estoque"><?= $produto['estoque'] > 0 ? 'Em estoque' : 'Indisponível' ?></p>
        <?php if ($produto['estoque'] > 0): ?>
          <button class="comprar">Adicionar ao carrinho</button>
        <?php else: ?>
          <button class="comprar" disabled>Indisponível</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<?php include 'footer.php';?>
</html>
