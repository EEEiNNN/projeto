<?php
// ATIVA A EXIBIÇÃO DE ERROS DO PHP - ESSENCIAL PARA DEBUG
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once 'conexao.php'; // Inclui a conexão segura com o banco

try {
    // ================== MUDANÇA CRÍTICA AQUI ==================
    // Esta nova consulta une a tabela `produto` com a `imagemproduto`
    // para buscar a URL da imagem principal de cada produto.
    $sql = "
        SELECT 
            p.id, 
            p.nome, 
            p.preco,  
            i.url_imagem AS imagem 
        FROM 
            produto p
        LEFT JOIN 
            imagemproduto i ON p.id = i.produto_id AND i.principal = 1
        WHERE
            p.ativo = 1
        ORDER BY 
            p.id DESC
    ";
    
    $stmt = $pdo->query($sql);
    // ==========================================================
    
    // Busca todos os resultados
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica se encontrou produtos
    if ($produtos) {
        // Retorna uma resposta de sucesso com a lista de produtos
        echo json_encode(['status' => 'success', 'products' => $produtos]);
    } else {
        // Retorna uma resposta de sucesso com uma lista vazia
        echo json_encode(['status' => 'success', 'products' => [], 'message' => 'Nenhum produto encontrado.']);
    }

} catch (Exception $e) {
    // Em caso de erro (ex: falha na consulta), retorna uma resposta de erro
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>