<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Ação inválida ou não especificada.'];
$action = $_POST['action'] ?? '';

switch ($action) {

    case 'login':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = trim($_POST['senha'] ?? '');

        if (!$email || empty($senha)) {
            $response['message'] = 'Por favor, preencha o email e a senha.';
            break;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha'])) {
                 if ($user['ativo'] !== 'Sim') {
                    $response['message'] = 'Esta conta está inativa.';
                    break;
                }
                
                if (isset($user['status']) && $user['status'] === 'pendente') {
                    $response['message'] = 'A sua conta está pendente. Por favor, aceda à página de cadastro para definir a sua senha.';
                    break;
                }

                session_regenerate_id(true);
                $_SESSION['id'] = $user['id'];
                $_SESSION['nome'] = $user['nome'];
                $_SESSION['nivel'] = $user['nivel'];

                $response['success'] = true;
                $response['message'] = 'Login bem-sucedido! A redirecionar...';
                
                $nivel = strtolower(trim($user['nivel']));
                if ($nivel === 'admin') {
                    $response['redirect'] = 'painel/index.php';
                } else {
                    $response['redirect'] = 'index.php';
                }
            } else {
                $response['message'] = 'Email ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro no servidor. Tente novamente.';
        }
        break;

    case 'check_email':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $response['message'] = 'Por favor, insira um email válido.';
            break;
        }

        try {
            $stmt = $pdo->prepare("SELECT status FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                if ($user['status'] === 'ativo') {
                    $response['message'] = 'Este email já está registado. Por favor, faça o login.';
                } else if ($user['status'] === 'pendente') {
                    $response['success'] = true;
                    $response['status'] = 'pending';
                } else {
                    $response['message'] = 'Esta conta encontra-se inativa.';
                }
            } else {
                $response['success'] = true;
                $response['status'] = 'new_user';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro no servidor ao verificar o email.';
        }
        break;

    case 'register':
        $nome = htmlspecialchars(trim($_POST['nome'] ?? ''));
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
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nome, email, senha, nivel, status, ativo) 
                 VALUES (?, ?, ?, 'user', 'ativo', 'Sim')"
            );
            if ($stmt->execute([$nome, $email, $senha_hash])) {
                $response['success'] = true;
                $response['message'] = 'Registo realizado com sucesso!';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao criar conta.';
        }
        break;

    case 'set_password':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = trim($_POST['senha'] ?? '');

        if (!$email || strlen($senha) < 6) {
            $response['message'] = 'A senha deve ter no mínimo 6 caracteres.';
            break;
        }

        try {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET senha = ?, status = 'ativo' WHERE email = ? AND status = 'pendente'");
            
            if ($stmt->execute([$senha_hash, $email])) {
                if ($stmt->rowCount() > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Senha definida com sucesso!';
                } else {
                    $response['message'] = 'Não foi possível atualizar a conta. Pode já estar ativa.';
                }
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro no servidor ao definir a senha.';
        }
        break;
}

echo json_encode($response);
exit;