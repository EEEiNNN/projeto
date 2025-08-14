<?php include 'header.php';?>
<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ben-David</title>
  <link rel="stylesheet" href="_css/produtos.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>

<!-- Produtos -->
<main>
  <section class="produtos-section">
    <?php
    // Verifica se uma categoria foi passada na URL
    $categoriaSelecionada = $_GET['categoria'] ?? null;

    if ($categoriaSelecionada) {
      echo "<h1 class='titulo-pagina'>Categoria: " . htmlspecialchars(ucfirst($categoriaSelecionada)) . "</h1>";
    } else {
      echo "<h1 class='titulo-pagina'>Nossos Produtos</h1>";
    }
    ?>
    
    <div class="produtos-grid-container">
      <div class="produtos-grid">

        <?php
        if ($categoriaSelecionada) {
          $stmt = $pdo->prepare("SELECT p.id, p.nome, p.preco, i.url_imagem, c.nome AS categoria
                                 FROM Produto p
                                 LEFT JOIN Categoria c ON p.categoria_id = c.id
                                 LEFT JOIN ImagemProduto i ON i.produto_id = p.id AND i.principal = 1
                                 WHERE p.ativo = 1 AND c.nome = ?");
          $stmt->execute([$categoriaSelecionada]);
        } else {
          $stmt = $pdo->query("SELECT p.id, p.nome, p.preco, i.url_imagem, c.nome AS categoria
                               FROM Produto p
                               LEFT JOIN Categoria c ON p.categoria_id = c.id
                               LEFT JOIN ImagemProduto i ON i.produto_id = p.id AND i.principal = 1
                               WHERE p.ativo = 1");
        }

        while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) :
        ?>
          <div class="produto-card">
            <a href="produto.php?id=<?= $produto['id'] ?>">
              <img src="<?= $produto['url_imagem'] ?? '_images/padrao.jpg' ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" />
              <h3><?= htmlspecialchars($produto['nome']) ?></h3>
              <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
              <button class="btn btn-primary">Adicionar ao Carrinho</button>
            </a>
          </div>
        <?php endwhile; ?>

      </div>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>
<!-- Scripts -->
<script src="_js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>