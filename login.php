<?php
require_once("conexao.php");

// Criar usuário administrador padrão se ainda não houver nenhum usuário
$nome_sistema = "Administrador";
$email_sistema = "admin@admin.com";
$endereco = "Endereço padrão";
$senha = '123';
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$query = $pdo->query("SELECT * FROM usuarios");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

if($linhas == 0) {
    $query = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, endereco, nivel, ativo, foto) 
        VALUES (:nome, :email, :senha, :endereco, 'admin', 'Sim', 'sem-foto.jpg')");
    $query->execute([
        ':nome' => $nome_sistema,
        ':email' => $email_sistema,
        ':senha' => $senha_hash,
        ':endereco' => $endereco
    ]);
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="_css/login.css">
        <link rel="shortcut icon" type="image/x-icon" href="_img/icone.svg">
    </head>
    <body>
        <div class="box">
            <div class="figura">
                <a href="index.php">B</a>
            </div>
            <span class="borderLine"></span>
            <form action="autenticar.php" method="POST">
                <h2>Entrar</h2>
                <div class="inputBox">
                    <input type="text" name="usuario" required="required">
                    <span>Login</span>
                    <i></i>
                </div>
                <div class="inputBox">
                    <input type="password" name="senha" required="required">
                    <span>Senha</span>
                    <i></i>
                </div>
                <div class="links">
                    <a href="">Esqueci a senha</a>
                    <a href="cadastrar.php">Cadastre-se</a>
                </div>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>
</html>