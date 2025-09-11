<?php
// alternar_status.php (produtos)
require_once '../../../conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['nivel'] != 'admin' || $_SERVER['REQUEST_METHOD'] != 'POST') {
    exit('Acesso negado.');
}

$id = $_POST['id'] ?? 0;

if ($id) {
    try {
        $stmt_check = $pdo->prepare("SELECT ativo FROM produto WHERE id = ?");
        $stmt_check->execute([$id]);
        $produto = $stmt_check->fetch();

        if ($produto) {
            $novo_status = $produto['ativo'] ? 0 : 1; // Inverte o status (1 para 0, 0 para 1)
            $stmt_update = $pdo->prepare("UPDATE produto SET ativo = ? WHERE id = ?");
            $stmt_update->execute([$novo_status, $id]);
            echo 'Status do produto alterado com sucesso!';
        } else {
            echo 'Produto não encontrado.';
        }
    } catch (PDOException $e) {
        echo 'Erro ao alterar o status do produto: ' . $e->getMessage();
    }
} else {
    echo 'ID do produto inválido.';
}
?>
