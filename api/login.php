<?php
header("Content-Type: application/json; charset=UTF-8");
include 'conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Email e senha são obrigatórios.']);
    exit;
}

try {
    $query = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $query->bindValue(':email', $email);
    $query->execute();

    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido
        // Aqui você pode gerar um token de sessão no futuro
        unset($usuario['senha']); // Não retorne a senha
        echo json_encode(['status' => 'success', 'user' => $usuario]);
    } else {
        // Credenciais inválidas
        echo json_encode(['status' => 'error', 'message' => 'Email ou senha inválidos.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>
