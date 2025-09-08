<?php
// Ativa a exibição de todos os erros do PHP para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
// Permite o método DELETE
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexao.php'; // Inclui a conexão segura

$method = $_SERVER['REQUEST_METHOD'];

// Requisições OPTIONS são enviadas por navegadores para verificar a permissão (CORS)
if ($method == "OPTIONS") {
    http_response_code(200);
    exit();
}

try {
    if ($method === 'GET') {
        // LÓGICA PARA BUSCAR O CARRINHO (sem alterações)
        if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
            throw new Exception("ID do usuário é obrigatório.", 400);
        }
        $userId = $_GET['user_id'];
        
        $sql = "SELECT p.id, p.nome, ip.url_imagem as imagem, i.quantidade, (p.preco * i.quantidade) as subtotal
                FROM itemcarrinho i
                JOIN produto p ON i.produto_id = p.id
                LEFT JOIN imagemproduto ip ON p.id = ip.produto_id AND ip.principal = 1
                JOIN carrinho c ON i.carrinho_id = c.id
                WHERE c.usuario_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalSql = "SELECT SUM(p.preco * i.quantidade) as total
                     FROM itemcarrinho i
                     JOIN produto p ON i.produto_id = p.id
                     JOIN carrinho c ON i.carrinho_id = c.id
                     WHERE c.usuario_id = :user_id";
        $totalStmt = $pdo->prepare($totalSql);
        $totalStmt->execute(['user_id' => $userId]);
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
        $total = $totalResult['total'] ?? 0;

        echo json_encode(['items' => $items, 'total' => $total]);

    } elseif ($method === 'POST') {
        // LÓGICA PARA ADICIONAR AO CARRINHO (sem alterações)
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!isset($input['user_id']) || !isset($input['product_id'])) {
            throw new Exception("ID do usuário e do produto são obrigatórios.", 400);
        }
        $userId = $input['user_id'];
        $productId = $input['product_id'];
        
        $stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $cart = $stmt->fetch();
        $cartId = null;
        if ($cart) {
            $cartId = $cart['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id, data_criacao) VALUES (:user_id, NOW())");
            $stmt->execute(['user_id' => $userId]);
            $cartId = $pdo->lastInsertId();
        }
        $stmt = $pdo->prepare("SELECT id, quantidade FROM itemcarrinho WHERE carrinho_id = :cart_id AND produto_id = :product_id");
        $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        $item = $stmt->fetch();
        if ($item) {
            $newQuantity = $item['quantidade'] + 1;
            $stmt = $pdo->prepare("UPDATE itemcarrinho SET quantidade = :quantity WHERE id = :item_id");
            $stmt->execute(['quantity' => $newQuantity, 'item_id' => $item['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO itemcarrinho (carrinho_id, produto_id, quantidade) VALUES (:cart_id, :product_id, 1)");
            $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        }
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Produto adicionado com sucesso.']);

    } elseif ($method === 'DELETE') {
        // --- NOVA LÓGICA PARA EXCLUIR ITEM DO CARRINHO ---
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['user_id']) || !isset($input['product_id'])) {
            throw new Exception("ID do usuário e do produto são obrigatórios para exclusão.", 400);
        }
        $userId = $input['user_id'];
        $productId = $input['product_id'];

        // Encontrar o carrinho_id do usuário
        $stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $cart = $stmt->fetch();

        if ($cart) {
            $cartId = $cart['id'];
            // Excluir o itemcarrinho correspondente
            $stmt = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = :cart_id AND produto_id = :product_id");
            $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
            
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Produto removido do carrinho.']);
        } else {
            throw new Exception("Carrinho não encontrado para este usuário.", 404);
        }

    } else {
        throw new Exception("Método não permitido.", 405);
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

