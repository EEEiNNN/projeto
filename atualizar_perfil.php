<?php
// Inicia a sessão e a conexão com a base de dados
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

// Segurança: Garante que apenas utilizadores logados e via POST acedam
if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$action = $_POST['action'] ?? '';

// Função para definir uma mensagem de feedback e redirecionar
function redirect_with_feedback($type, $message) {
    $_SESSION['feedback'] = ['type' => $type, 'message' => $message];
    header('Location: perfil.php');
    exit;
}

// Lógica para atualizar dados pessoais
if ($action === 'update_details') {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    if (empty($nome)) {
        redirect_with_feedback('error', 'O nome não pode estar vazio.');
    }

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ?, endereco = ? WHERE id = ?");
        $stmt->execute([$nome, $telefone, $endereco, $user_id]);

        // Atualiza o nome na sessão para que o header mostre o nome novo
        $_SESSION['nome'] = $nome;

        redirect_with_feedback('success', 'Dados atualizados com sucesso!');
    } catch (PDOException $e) {
        redirect_with_feedback('error', 'Ocorreu um erro ao atualizar os seus dados.');
    }
}

// Lógica para alterar a senha
if ($action === 'change_password') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if (empty($senha_atual) || empty($nova_senha) || empty($confirma_senha)) {
        redirect_with_feedback('error', 'Todos os campos de senha são obrigatórios.');
    }
    if ($nova_senha !== $confirma_senha) {
        redirect_with_feedback('error', 'A nova senha e a confirmação não correspondem.');
    }
    if (strlen($nova_senha) < 6) {
        redirect_with_feedback('error', 'A nova senha deve ter no mínimo 6 caracteres.');
    }

    try {
        // 1. Busca a senha atual no banco de dados
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // 2. Verifica se a senha atual fornecida está correta
        if (!$user || !password_verify($senha_atual, $user['senha'])) {
            redirect_with_feedback('error', 'A senha atual está incorreta.');
        }

        // 3. Se tudo estiver correto, atualiza para a nova senha
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->execute([$nova_senha_hash, $user_id]);

        redirect_with_feedback('success', 'Senha alterada com sucesso!');
    } catch (PDOException $e) {
        redirect_with_feedback('error', 'Ocorreu um erro ao alterar a sua senha.');
    }
}

// Se nenhuma ação válida for encontrada, redireciona de volta
header('Location: perfil.php');
exit;