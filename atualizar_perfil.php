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
    $complemento = trim($_POST['complemento'] ?? ''); 

    if (empty($nome)) {
        redirect_with_feedback('error', 'O nome não pode estar vazio.');
    }

    try {
        $pdo->beginTransaction();
        if (!empty($cep) && !empty($rua)) {
            if ($endereco_id) { 
                $stmtEnd = $pdo->prepare("UPDATE endereco SET cep=?, rua=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE id=? AND usuario_id=?");
                $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $endereco_id, $user_id]);
            } else { 
                $stmtCheck = $pdo->prepare("SELECT id FROM endereco WHERE usuario_id = ?");
                $stmtCheck->execute([$user_id]);
                $existingAddr = $stmtCheck->fetch();

                if ($existingAddr) {
                    $stmtEnd = $pdo->prepare("UPDATE endereco SET cep=?, rua=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE usuario_id=?");
                    $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $user_id]);
                } else {
                    $stmtEnd = $pdo->prepare("INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtEnd->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $user_id]);
                }
            }
        }
        $stmtUser = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ?");
        $stmtUser->execute([$nome, $telefone, $user_id]);

        $pdo->commit();

        $_SESSION['nome'] = $nome; 
        redirect_with_feedback('success', 'Dados atualizados com sucesso!');

    } catch (PDOException $e) {
        $pdo->rollBack();
        // Para depuração, pode ser útil ver o erro real:
        // redirect_with_feedback('error', 'Ocorreu um erro ao atualizar os seus dados: ' . $e->getMessage());
        redirect_with_feedback('error', 'Ocorreu um erro ao atualizar os seus dados.');
    }
}
header('Location: perfil.php');
exit;
?>