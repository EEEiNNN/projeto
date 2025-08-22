<?php
require_once("../../../conexao.php");

// Dados do utilizador
$id = $_POST['id'] ?? '';
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$nivel = $_POST['nivel'] ?? 'user';

// Dados do endereço
$endereco_id = $_POST['endereco_id'] ?? '';
$cep = $_POST['cep'] ?? '';
$rua = $_POST['rua'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? '';
$bairro = $_POST['bairro'] ?? '';
$cidade = $_POST['cidade'] ?? '';
$estado = $_POST['estado'] ?? '';

if (empty($nome) || empty($email)) {
    echo "Nome e email são obrigatórios!";
    exit();
}

try {
    // Inicia uma transação
    $pdo->beginTransaction();

    // Passo 1: Inserir ou Atualizar o Endereço
    // Apenas insere/atualiza se algum campo de endereço for preenchido
    if (!empty($cep) || !empty($rua)) {
        if (empty($endereco_id)) {
            // Insere um novo endereço
            $stmtEnd = $pdo->prepare("INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $id]);
            $endereco_id = $pdo->lastInsertId();
        } else {
            // Atualiza um endereço existente
            $stmtEnd = $pdo->prepare("UPDATE endereco SET cep = ?, rua = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ? WHERE id = ?");
            $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $endereco_id]);
        }
    }

    // Passo 2: Inserir ou Atualizar o Utilizador
    if (empty($id)) {
        // Insere um novo utilizador, já associando o endereco_id (pode ser null)
        $stmtUser = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, nivel, endereco_id, status) VALUES (?, ?, ?, ?, ?, 'pendente')");
        $stmtUser->execute([$nome, $email, $telefone, $nivel, $endereco_id ?: null]);
    } else {
        // Atualiza um utilizador existente
        $stmtUser = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, nivel = ?, endereco_id = ? WHERE id = ?");
        $stmtUser->execute([$nome, $email, $telefone, $nivel, $endereco_id ?: null, $id]);
    }

    // Se tudo correu bem, confirma as alterações
    $pdo->commit();
    echo "Salvo com Sucesso";

} catch (Exception $e) {
    // Se algo deu errado, desfaz tudo
    $pdo->rollBack();
    echo "Ocorreu um erro ao salvar: " . $e->getMessage();
}
?>