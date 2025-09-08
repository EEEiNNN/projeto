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
    // Inicia uma transação para garantir que tudo seja salvo ou nada seja salvo.
    $pdo->beginTransaction();

    // Passo 1: Inserir ou Atualizar o Endereço
    // Apenas insere/atualiza se algum campo de endereço for preenchido
    if (!empty($cep) || !empty($rua)) {
        if (empty($endereco_id)) {
            // Inserir novo endereço, associado ao utilizador (novo ou existente)
            $stmtEnd = $pdo->prepare(
                "INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $usuario_id_a_usar]);
        } else {
            // Atualizar endereço existente, garantindo que pertence ao utilizador correto
            $stmtEnd = $pdo->prepare(
                "UPDATE endereco SET cep = ?, rua = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ? WHERE id = ? AND usuario_id = ?"
            );
            $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $endereco_id, $usuario_id_a_usar]);
        }
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