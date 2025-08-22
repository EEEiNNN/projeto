<?php include 'header.php'; ?>
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

<main style="background-color: #fdf9f9;">
  <section class="produtos-section">
    <?php
    $categoriaSelecionada = $_GET['categoria'] ?? null;
    ?>
    
    <div class="produtos-grid-container">
      <div class="produtos-grid">
  
        <?php
        $produtoModel = new Produto($pdo);
        if ($categoriaSelecionada) {
          $produtos = $produtoModel->getByCategoriaNome($categoriaSelecionada);
        } else {
          $produtos = $produtoModel->getAllAtivos();
        }

        foreach ($produtos as $produto) :
          $urlImagem = $produtoModel->getImagemPrincipal($produto['id']) ?? '_images/padrao.jpg';
        ?>
          <div class="produto-card">
            <a href="produto.php?id=<?= $produto['id'] ?>">
              <img src="<?= $urlImagem ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" />
            </a>
            <div class="produto-card-info">
              <h3><?= htmlspecialchars($produto['nome']) ?></h3>
              <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
              
              <button class="btn btn-primary btn-add-carrinho" data-produto-id="<?= $produto['id'] ?>">
                Adicionar ao Carrinho
              </button>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </section>
</main>
<script src="_js/script.js"></script>
<script src="js/shop_events.js"></script> 
<?php include 'footer.php'; ?>

