<?php
// Ficheiro: api/autenticar.php
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => 'Ação inválida.'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = trim($_POST['senha'] ?? '');
        if (!$email || empty($senha)) {
            $response['message'] = 'Preencha todos os campos.';
            break;
        }

        $stmt = $pdo->prepare("SELECT id, nome, email, nivel, senha FROM usuarios WHERE email = ? AND status = 'ativo'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($senha, $user['senha'])) {
            unset($user['senha']); // Nunca devolver a senha
            $response = [
                'success' => true,
                'message' => 'Login bem-sucedido!',
                'user' => $user
            ];
        } else {
            $response['message'] = 'Email ou senha incorretos.';
        }
        break;
    
    // Adicione aqui os outros 'case' do seu ficheiro autenticar.php original,
    // como 'register', 'check_email', 'set_password', garantindo que todos
    // devolvem uma resposta com `echo json_encode($response);`
}

echo json_encode($response);
?>  