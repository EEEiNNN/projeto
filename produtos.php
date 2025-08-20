<?php include 'header.php'; ?>
<?php include 'conexao.php'; ?>
<?php include 'models/Produto.php'; ?>
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
<main style="background-color: #fdf9f9;">
  <section class="produtos-section">
    <?php
    // Verifica se uma categoria foi passada na URL
    $categoriaSelecionada = $_GET['categoria'] ?? null;

    
    ?>
    
    <div class="produtos-grid-container">
      <div class="produtos-grid">

        <?php
        // Instancia o model Produto
        $produtoModel = new Produto($pdo);

        // Busca os produtos pelo model
        if ($categoriaSelecionada) {
          $produtos = $produtoModel->getByCategoriaNome($categoriaSelecionada);
        } else {
          $produtos = $produtoModel->getAllAtivos();
        }

        // Loop para exibir os produtos
        foreach ($produtos as $produto) :
          // Pega a imagem principal (ou imagem padrão caso não exista)
          $urlImagem = $produtoModel->getImagemPrincipal($produto['id']) ?? '_images/padrao.jpg';
        ?>
          <div class="produto-card">
            <a href="produto.php?id=<?= $produto['id'] ?>">
              <img src="<?= $urlImagem ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" />
              <h3><?= htmlspecialchars($produto['nome']) ?></h3>
              <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
              <button class="btn btn-primary">Adicionar ao Carrinho</button>
            </a>
          </div>
        <?php endforeach; ?>

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
