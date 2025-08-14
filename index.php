<?php include 'header.php';?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ben-David</title>
  <link rel="stylesheet" href="_css/style.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <main>
    <section class="banner">
      <div class="bannertext">
        <h1>NOVA COLEÇÃO</h1>
        <img src="" alt="">
        <button>Ver mais</button>
      </div>
    </section>

    <div class="infocard-container">
      <div>
        <div class="infocard">
          <img src="#" alt="Imagem 1">
        </div>
        <a href="produtos.php?categoria=aneis"><button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Anéis</button></a>
      </div>
      <div>
        <div class="infocard">
          <img src="#" alt="Imagem 2">
        </div>
        <a href="produtos.php?categoria=brincos"><button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Brincos</button></a>
      </div>
      <div>
        <div class="infocard">
          <img src="#" alt="Imagem 3">
        </div>
        <a href="produtos.php?categoria=colares"><button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Colares</button></a>
      </div>
      <div>
        <div class="infocard">
          <img src="#" alt="Imagem 4">
        </div>
        <a href="produtos.php?categoria=pulseiras"><button type="button" class="btn btn-primary" style="border: none; margin-top: 10px;">Pulseiras</button></a>
      </div>
    </div>

    
    <?php include 'footer.php';?>
  </main>
  <script src="_js/script.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>