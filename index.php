<?php 
include 'header.php';
function getImagemDaCategoria(string $nomeCategoria, PDO $pdo): string
{
    $sql = "
        SELECT i.url_imagem
        FROM produto p
        INNER JOIN categoria c ON c.id = p.categoria_id
        LEFT JOIN imagemproduto i ON i.produto_id = p.id
        WHERE c.nome = :nomeCategoria
          AND c.ativo = 1
          AND p.ativo = 1
        ORDER BY
          p.data_cadastro ASC,
          p.id ASC,
          i.principal DESC,
          i.id ASC
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nomeCategoria' => $nomeCategoria]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['url_imagem'])) {
        return $row['url_imagem'];
    }

    // Fallback caso não exista produto/imagem para a categoria
    return "_images/sem-imagem.jpg";
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ben-David</title>

  <!-- CSS do projeto -->
  <link rel="stylesheet" href="_css/style.css"/>

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
</head>
<body>
  <main>
    <section class="banner">
      <div class="bannertext">
        <h1>NOVA COLEÇÃO</h1>
        <!-- se desejar, você pode puxar uma imagem de destaque aqui também -->
        <img src="" alt="">
        <a href="http://localhost/php/projeto_integrador/projeto2/produtos.php"><button>Ver mais</button></a>
      </div>
    </section>

    <div class="infocard-container">
      <!-- Anéis -->
      <div>
        <div class="infocard">
          <img
            src="<?= htmlspecialchars(getImagemDaCategoria('aneis', $pdo)); ?>"
            alt="Anéis"
            loading="lazy"
          >
        </div>
        <a href="produtos.php?categoria=aneis">
          <button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Anéis</button>
        </a>
      </div>

      <!-- Brincos -->
      <div>
        <div class="infocard">
          <img
            src="<?= htmlspecialchars(getImagemDaCategoria('brincos', $pdo)); ?>"
            alt="Brincos"
            loading="lazy"
          >
        </div>
        <a href="produtos.php?categoria=brincos">
          <button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Brincos</button>
        </a>
      </div>

      <!-- Colares -->
      <div>
        <div class="infocard">
          <img
            src="<?= htmlspecialchars(getImagemDaCategoria('colares', $pdo)); ?>"
            alt="Colares"
            loading="lazy"
          >
        </div>
        <a href="produtos.php?categoria=colares">
          <button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Colares</button>
        </a>
      </div>

      <!-- Pulseiras -->
      <div>
        <div class="infocard">
          <img
            src="<?= htmlspecialchars(getImagemDaCategoria('pulseiras', $pdo)); ?>"
            alt="Pulseiras"
            loading="lazy"
          >
        </div>
        <a href="produtos.php?categoria=pulseiras">
          <button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Pulseiras</button>
        </a>
      </div>
    </div>
  </main>
 <script src="_js/script.js"></script>
<?php include 'footer.php'; ?>