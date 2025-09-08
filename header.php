<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <link rel="icon" type="image/png" href="_images/logo.jpeg">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="_css/header.css" rel="stylesheet">
    
    </head>
<body>

<header>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="produtosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-bars" style="color: #A1CCA5; font-size: 24px;"></i>
          </a>
          <ul class="dropdown-menu" aria-labelledby="produtosDropdown">
            <li><a class="dropdown-item" href="produtos.php?categoria=aneis">Anéis</a></li>
            <li><a class="dropdown-item" href="produtos.php?categoria=brincos">Brincos</a></li>
            <li><a class="dropdown-item" href="produtos.php?categoria=colares">Colares</a></li>
            <li><a class="dropdown-item" href="produtos.php?categoria=pulseiras">Pulseiras</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <div class="logo">
    <a href="index.php">
      <h1>Ben-David</h1>
    </a>
  </div>
  
  <div class="actions-wrapper">
    <ul>
      <li class="profile-icon">
        <a href="login.php" class="icon-button" title="Perfil">
          <svg fill="none" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 13.75c-2.34 0-4.23-2.8-4.23-5.12A4.17 4.17 0 0 1 12 4.25a4.17 4.17 0 0 1 4.24 4.38c0 2.37-1.89 5.12-4.24 5.12m0-1.5c1.41 0 2.74-2 2.74-3.62a2.74 2.74 0 0 0-5.48 0c0 1.58 1.33 3.62 2.74 3.62M23.75 12A11.76 11.76 0 0 0 11.3.27C4.88.735.001 6.23.3 12.66a11.65 11.65 0 0 0 2.33 6.44l.44-.33a1 1 0 0 0 .25-1.3A10.16 10.16 0 0 1 1.75 12a10.25 10.25 0 1 1 18.92 5.48 1 1 0 0 0 .26 1.29l.44.33a11.6 11.6 0 0 0 2.38-7.1M12 23.75a11.74 11.74 0 0 1-7.79-3l-.06-.05a.77.77 0 0 1-.1-.88 8.1 8.1 0 0 1 1.81-2.73 6.5 6.5 0 0 1 4.78-1.88h2.72a6.5 6.5 0 0 1 4.78 1.88 8 8 0 0 1 1.74 2.56.78.78 0 0 1 0 1 .7.7 0 0 1-.12.12A11.78 11.78 0 0 1 12 23.75M5.62 20a10.24 10.24 0 0 0 12.76 0 6.3 6.3 0 0 0-1.3-1.83 5 5 0 0 0-3.72-1.44h-2.72a5 5 0 0 0-3.72 1.44A6.4 6.4 0 0 0 5.62 20" fill="#A1CCA5"></path></svg>
        </a>
      </li>
      <li class="cart-icon">
        <a href="carrinho.php" class="icon-button" id="cartToggle" title="Carrinho">
          <svg height="24" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 6.25h-3.25c-.68-3.62-2.53-6-4.75-6s-4.07 2.38-4.75 6H4a.76.76 0 0 0-.75.75v12A4.75 4.75 0 0 0 8 23.75h8A4.75 4.75 0 0 0 20.75 19V7a.76.76 0 0 0-.75-.75m-8-4.5c1.38 0 2.66 1.84 3.22 4.5H8.78c.56-2.66 1.84-4.5 3.22-4.5M19.25 19A3.26 3.26 0 0 1 16 22.25H8A3.26 3.26 0 0 1 4.75 19V7.75H7l-.24 2.16.49.06a1 1 0 0 0 1.12-.87l.17-1.35h6.92l.17 1.35a1 1 0 0 0 1.12.87l.49-.06L17 7.75h2.28z" fill="#A1CCA5"></path></svg>
        </a>
      </li>
    </ul>
  </div>
</header>