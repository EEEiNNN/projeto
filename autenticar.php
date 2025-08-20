<?php
// auth.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

// Verificamos se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Ação inválida.'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = trim($_POST['senha'] ?? '');

        if (!$email || empty($senha)) {
            $response['message'] = 'Por favor, preencha todos os campos.';
            break;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['ativo'] === 'Sim' && password_verify($senha, $user['senha'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_level'] = $user['nivel'];

                $response['success'] = true;
                $response['message'] = 'Login bem-sucedido!';
                $response['redirect'] = ($user['nivel'] === 'admin') ? 'painel/index.php' : 'index.php';
            } else {
                $response['message'] = 'Email, senha ou conta inativa.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro no servidor. Tente novamente.';
            // Em um ambiente de produção, você logaria o erro em vez de exibi-lo.
            // error_log('Erro de login: ' . $e->getMessage());
        }
        break;

    case 'register':
        $nome = htmlspecialchars(trim($_POST['usuario'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = trim($_POST['senha'] ?? '');

        if (empty($nome) || !$email || empty($senha)) {
            $response['message'] = 'Todos os campos são obrigatórios.';
            break;
        }
        if (strlen($senha) < 6) {
            $response['message'] = 'A senha deve ter no mínimo 6 caracteres.';
            break;
        }

        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $response['message'] = 'Este e-mail já está cadastrado.';
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "INSERT INTO usuarios (nome, email, senha, nivel, ativo, foto) 
                     VALUES (:nome, :email, :senha, 'user', 'Sim', 'sem-foto.jpg')"
                );
                if ($stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $senha_hash])) {
                    $response['success'] = true;
                    $response['message'] = 'Cadastro realizado com sucesso!';
                } else {
                    $response['message'] = 'Erro ao criar conta. Tente novamente.';
                }
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro no servidor. Tente novamente.';
            // error_log('Erro de registro: ' . $e->getMessage());
        }
        break;
}

echo json_encode($response);
exit;