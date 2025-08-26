<?php
require_once("../../../conexao.php");
header('Content-Type: application/json');

$usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
$response = ['success' => false, 'enderecos' => []];

if ($usuario_id) {
    try {
        $stmt = $pdo->prepare("SELECT id, rua, numero, cidade FROM endereco WHERE usuario_id = ? ORDER BY id DESC");
        $stmt->execute([$usuario_id]);
        $enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['enderecos'] = $enderecos;
    } catch (Exception $e) {
        // Em caso de erro, a resposta de falha padrão será enviada.
    }
}

echo json_encode($response);
?>