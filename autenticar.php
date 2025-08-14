<?php
@session_start();
require_once("conexao.php");

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

$query = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$query->bindValue(":email", $usuario);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

if ($linhas > 0 && password_verify($senha, $res[0]['senha'])) {
    $_SESSION['nome'] = $res[0]['nome'];
    $_SESSION['id'] =  $res[0]['id'];
    $_SESSION['nivel'] = $res[0]['nivel'];

    if ($res[0]['nivel'] == 'admin') {
        echo '<script>window.location="painel/index.php"</script>';
    } else {
        echo '<script>window.location="index.php"</script>';
    }
} else {
    echo '<script>window.alert("Dados incorretos!")</script>';
    echo '<script>window.location="login.php"</script>';
}
?>
