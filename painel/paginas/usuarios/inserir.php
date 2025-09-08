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

    $usuario_id_a_usar = $id;

    // --- LÓGICA DE UTILIZADOR ---
    if (empty($id)) {
        // CASO 1: CRIAR NOVO UTILIZADOR
        // [CORREÇÃO] A query não inclui mais a coluna 'endereco_id'
        $stmtUser = $pdo->prepare(
            "INSERT INTO usuarios (nome, email, telefone, nivel, status) VALUES (?, ?, ?, ?, 'pendente')"
        );
        $stmtUser->execute([$nome, $email, $telefone, $nivel]);

        // **VERIFICAÇÃO CRÍTICA**: Confirmar que o utilizador foi criado antes de prosseguir.
        $novo_id = $pdo->lastInsertId();
        if (empty($novo_id)) {
            throw new Exception("Falha crítica: Não foi possível criar o novo utilizador na base de dados.");
        }
        $usuario_id_a_usar = $novo_id;

    } else {
        // CASO 2: ATUALIZAR UTILIZADOR EXISTENTE
        $stmtUser = $pdo->prepare(
            "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, nivel = ? WHERE id = ?"
        );
        $stmtUser->execute([$nome, $email, $telefone, $nivel, $id]);
    }

    // --- LÓGICA DE ENDEREÇO ---
    // Só executa se houver dados de endereço e um ID de utilizador válido
    if ((!empty($cep) || !empty($rua)) && !empty($usuario_id_a_usar)) {
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