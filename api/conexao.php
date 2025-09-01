<?php
date_default_timezone_set('America/Sao_Paulo');

$host = 'localhost';
$dbname = 'projeto';
$username = 'root';
$password = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch(PDOException $e) {
    // Para uma API, é melhor retornar um erro JSON
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
    exit;
}

function isLoggedIn() {
    return (isset($_SESSION['id']) && !empty($_SESSION['id']));
}
?>