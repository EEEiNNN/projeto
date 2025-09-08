<?php
// Ativa a exibição de todos os erros do PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Verifica se o ID do produto foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'ID do produto não fornecido.']);
    exit();
}

$productId = $_GET['id'];

try {
    require_once 'conexao.php'; // Inclui a conexão segura

    $sql = "
        SELECT 
            p.id, 
            p.nome, 
            p.descricao, 
            p.preco, 
            p.estoque,
            img.url_imagem AS imagem
        FROM produto p
        LEFT JOIN imagemproduto img ON p.id = img.produto_id AND img.principal = 1
        WHERE p.id = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Produto encontrado
        http_response_code(200);
        echo json_encode(['status' => 'success', 'product' => $product]);
    } else {
        // Produto não encontrado
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado.']);
    }
} catch (PDOException $e) {
    // Erro de banco de dados
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Outros erros
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>

