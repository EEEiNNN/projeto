<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$action = $_POST['action'] ?? '';

function redirect_with_feedback($type, $message) {
    $_SESSION['feedback'] = ['type' => $type, 'message' => $message];
    header('Location: perfil.php');
    exit;
}

if ($action === 'update_details') {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    
    $endereco_id = $_POST['endereco_id'] ?: null;
    $cep = trim($_POST['cep']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);

    if (empty($nome)) {
        redirect_with_feedback('error', 'O nome não pode estar vazio.');
    }

    try {
        $pdo->beginTransaction();

        if ($endereco_id) { 
            $stmtEnd = $pdo->prepare("UPDATE endereco SET cep=?, rua=?, numero=?, bairro=?, cidade=?, estado=? WHERE id=? AND usuario_id=?");
            $stmtEnd->execute([$cep, $rua, $numero, $bairro, $cidade, $estado, $endereco_id, $user_id]);
        } elseif (!empty($cep) || !empty($rua)) { 
            $stmtEnd = $pdo->prepare("INSERT INTO endereco (cep, rua, numero, bairro, cidade, estado, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtEnd->execute([$cep, $rua, $numero, $bairro, $cidade, $estado, $user_id]);
            $endereco_id = $pdo->lastInsertId();
        }

        $stmtUser = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ?, endereco_id = ? WHERE id = ?");
        $stmtUser->execute([$nome, $telefone, $endereco_id, $user_id]);

        $pdo->commit();

        $_SESSION['nome'] = $nome;
        redirect_with_feedback('success', 'Dados atualizados com sucesso!');

    } catch (PDOException $e) {
        $pdo->rollBack();
        redirect_with_feedback('error', 'Ocorreu um erro ao atualizar os seus dados.');
    }
}


header('Location: perfil.php');
exit;
?>