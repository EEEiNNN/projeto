<?php
require_once("../../../conexao.php");

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$nivel = $_POST['nivel'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$id = $_POST['id'] ?? '';

// Evita emails duplicados ao inserir
if ($id == "") {
    $query = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $query->bindValue(":email", $email);
    $query->execute();
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($res) > 0) {
        echo "Email já cadastrado!";
        exit();
    }

    $query = $pdo->prepare("INSERT INTO usuarios SET nome = :nome, email = :email, telefone = :telefone, nivel = :nivel, endereco = :endereco");
} else {
    $query = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, nivel = :nivel, endereco = :endereco WHERE id = :id");
    $query->bindValue(":id", $id);
}

$query->bindValue(":nome", $nome);
$query->bindValue(":email", $email);
$query->bindValue(":telefone", $telefone);
$query->bindValue(":nivel", $nivel);
$query->bindValue(":endereco", $endereco);

$query->execute();

echo "Salvo com Sucesso";
?>