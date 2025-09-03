<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Ajuste o caminho conforme a estrutura do seu servidor
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config.php'; // Onde sua classe Payload está

use \App\Pix\Payload;

if (!isset($_GET['total']) || !is_numeric($_GET['total']) || $_GET['total'] <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Valor total inválido ou ausente.']);
    exit;
}

$total = (float)$_GET['total'];

try {
    // Instancia a classe Payload do seu arquivo config.php
    $obPayload = (new Payload)
        // EDITAR: Coloque sua chave PIX aqui
        ->setPixKey('seu-email-cpf-ou-cnpj') 
        
        // EDITAR: Coloque a descrição que aparecerá para o cliente
        ->setDescription('Pagamento Online - Pedido App') 
        
        // EDITAR: Coloque o nome da sua loja
        ->setMerchantName('Minha Loja') 
        
        // EDITAR: Coloque a cidade da sua loja
        ->setMerchantCity('CIDADE') 
        
        ->setAmount($total)
        
        // Gera um ID de transação único
        ->setTxid('APP' . uniqid()); 

    // Gera o código "Copia e Cola"
    $payloadQrCode = $obPayload->getPayload();

    http_response_code(200);
    echo json_encode(['status' => 'success', 'payload' => $payloadQrCode]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro ao gerar o código PIX: ' . $e->getMessage()]);
}
?>

