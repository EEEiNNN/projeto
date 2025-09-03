<?php
// Define o cabeçalho como JSON para a resposta
header('Content-Type: application/json');

// Inclui o arquivo de conexão segura
require_once 'conexao.php';

// Ativa a exibição de todos os erros do PHP para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = [];

// A API de login só deve aceitar requisições do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    $response = ['status' => 'error', 'message' => 'Método não permitido. Utilize POST.'];
    echo json_encode($response);
    exit();
}

// Pega os dados enviados pelo Flutter
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    http_response_code(400); // Bad Request
    $response = ['status' => 'error', 'message' => 'E-mail e senha são obrigatórios.'];
    echo json_encode($response);
    exit();
}

try {
    // Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    // Verifica se o usuário existe E se a senha criptografada bate
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido
        http_response_code(200);

        // --- MUDANÇA PRINCIPAL AQUI ---
        // Agora, a resposta inclui os dados do usuário
        $response = [
            'status' => 'success',
            'message' => 'Login bem-sucedido!',
            'user' => [
                'id' => (int)$usuario['id'], // Converte o ID para inteiro
                'nome' => $usuario['nome'],
                'email' => $usuario['email']
            ]
        ];
    } else {
        // Credenciais inválidas
        http_response_code(401); // Unauthorized
        $response = ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
    }

} catch (PDOException $e) {
    // Erro interno do servidor (problema no banco de dados)
    http_response_code(500);
    $response = ['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()];
}

// Retorna a resposta final em formato JSON
echo json_encode($response);
?>