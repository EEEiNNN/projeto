<?php
// alternar_status.php (usuarios)
require_once '../../../conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['nivel'] != 'admin' || $_SERVER['REQUEST_METHOD'] != 'POST') {
    exit('Acesso negado.');
}

$id = $_POST['id'] ?? 0;

if ($id) {
    // Previne que o admin desative a si mesmo
    if ($id == $_SESSION['id_usuario']) {
        exit('Você não pode desativar sua própria conta.');
    }

    try {
        $stmt_check = $pdo->prepare("SELECT ativo FROM usuarios WHERE id = ?");
        $stmt_check->execute([$id]);
        $usuario = $stmt_check->fetch();

        if ($usuario) {
            $novo_status = ($usuario['ativo'] == 'Sim') ? 'Não' : 'Sim'; // Inverte o status
            $stmt_update = $pdo->prepare("UPDATE usuarios SET ativo = ? WHERE id = ?");
            $stmt_update->execute([$novo_status, $id]);
            echo 'Status do usuário alterado com sucesso!';
        } else {
            echo 'Usuário não encontrado.';
        }
    } catch (PDOException $e) {
        echo 'Erro ao alterar o status do usuário: ' . $e->getMessage();
    }
} else {
    echo 'ID do usuário inválido.';
}
?>
