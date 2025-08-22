<?php
require_once("../../../conexao.php");

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$nivel = $_POST['nivel'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$id = $_POST['id'] ?? '';

// Validação básica para segurança
if (empty($nome) || empty($email) || empty($nivel)) {
    echo "Nome, email e nível são obrigatórios!";
    exit();
}

// Lógica para INSERIR um novo utilizador (criado pelo admin)
if ($id == "") {
    // Evita emails duplicados
    $query = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $query->bindValue(":email", $email);
    $query->execute();
    if ($query->rowCount() > 0) {
        echo "Email já registado!";
        exit();
    }

    // [ALTERAÇÃO] Modificamos a query para incluir 'senha' e 'status'
    $query = $pdo->prepare("INSERT INTO usuarios 
        (nome, email, telefone, nivel, endereco, senha, status, ativo) VALUES 
        (:nome, :email, :telefone, :nivel, :endereco, :senha, :status, 'Sim')");

    // [ALTERAÇÃO] Definimos a senha como NULL (vazia)
    $query->bindValue(":senha", NULL, PDO::PARAM_NULL);
    // [ALTERAÇÃO] Definimos o status como 'pendente'
    $query->bindValue(":status", 'pendente');

} 
// Lógica para ATUALIZAR um utilizador existente (não mexe na senha)
else {
    $query = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, nivel = :nivel, endereco = :endereco WHERE id = :id");
    $query->bindValue(":id", $id);
}

// Binds comuns para INSERT e UPDATE
$query->bindValue(":nome", $nome);
$query->bindValue(":email", $email);
$query->bindValue(":telefone", $telefone);
$query->bindValue(":nivel", $nivel);
$query->bindValue(":endereco", $endereco);

$query->execute();

echo "Salvo com Sucesso";
?>