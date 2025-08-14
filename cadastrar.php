<?php
require_once("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["usuario"];
    $email = $_POST["email"];
    $password = $_POST["senha"];
  
    $senha_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $check->execute([':email' => $email]);
        $exists = $check->fetchColumn();

        if ($exists > 0) {
            echo "<script>alert('Este e-mail já está cadastrado!');</script>";
        } else {
            $query = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, endereco, nivel, ativo, foto) 
                VALUES (:nome, :email, :senha, NULL, 'user', 'Sim', 'sem-foto.jpg')");
            $query->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senha_hash
            ]);
            echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
    <link rel="stylesheet" href="_css/login.css">
    <link rel="shortcut icon" type="image/x-icon" href="_img/icone.svg">
</head>
<body>
    <div class="box" style="height: 500px;">
        <div class="figura">
            <a href="index.php">B</a>
        </div>
        <span class="borderLine"></span>
        <form action="" method="POST">
            <h2>Cadastrar</h2>
            <div class="inputBox">
                <input type="text" name="usuario" required>
                <span>Nome</span>
                <i></i>
            </div>
            <div class="inputBox">
                <input type="text" name="email" required>
                <span>Email</span>
                <i></i>
            </div>
            <div class="inputBox">
                <input type="password" name="senha" required>
                <span>Senha</span>
                <i></i>
            </div>
            <div class="links">
                    <a href="login.php">Login</a>
            </div>
            <input type="submit" value="Cadastrar" style="padding: 9px;">
        </form>
    </div>
</body>
</html>
