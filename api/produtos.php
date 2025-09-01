<?php
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $categoria = $_GET['categoria'] ?? null;
    $sql = "
        SELECT p.id, p.nome, p.descricao, p.preco, p.estoque, c.nome as categoria, i.url_imagem 
        FROM produto p
        LEFT JOIN categoria c ON p.categoria_id = c.id
        LEFT JOIN imagemproduto i ON p.id = i.produto_id AND i.principal = 1
        WHERE p.ativo = 1
    ";
    
    if ($categoria) {
        $sql .= " AND c.nome = :categoria";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':categoria' => $categoria]);
    } else {
        $stmt = $pdo->query($sql);
    }
    
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $produtos]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no servidor ao buscar produtos.']);
}
?>