<?php
// ATIVA A EXIBIÇÃO DE ERROS DO PHP - ESSENCIAL PARA DEBUG
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Tenta incluir o arquivo de conexão
try {
    require_once 'conexao.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro crítico: Falha ao incluir o arquivo de conexão. ' . $e->getMessage()]);
    exit();
}

// A API de login só deve aceitar requisições do tipo POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido. Utilize POST.']);
    exit();
}

try {
    // Pega o e-mail e a senha enviados pelo Flutter
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validação básica
    if (empty($email) || empty($senha)) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'E-mail e senha são obrigatórios.']);
        exit();
    }

    // Prepara a consulta SQL para evitar injeção de SQL
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado e se a senha está correta
    // ATENÇÃO: Este exemplo assume que a senha no banco NÃO está criptografada.
    // O ideal é usar password_verify() com senhas hasheadas com password_hash().
    if ($usuario && $senha == $usuario['senha']) {
        echo json_encode(['status' => 'success', 'message' => 'Login bem-sucedido!']);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'E-mail ou senha inválidos.']);
    }

} catch (PDOException $e) {
    // Captura erros específicos de banco de dados
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Captura outros erros inesperados
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro inesperado no servidor: ' . $e->getMessage()]);
}
?>