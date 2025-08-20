<?php

date_default_timezone_set('America/Sao_Paulo');

// Dados de Conexão do Banco Local

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
    die("Erro na conexão com o banco de dados. Tente novamente mais tarde.");
}

// Função para verificar se usuário está logado
function isLoggedIn() {
    return (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) || 
           (isset($_SESSION['id']) && !empty($_SESSION['id']));
}

// Função para verificar se usuário é admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Função para verificar se usuário é comum
function isComum() {
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'comum';
}

// Função para obter dados do usuário logado
function getLoggedUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? $_SESSION['id'] ?? null,
            'nome' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'tipo' => $_SESSION['user_type'] ?? 'comum'
        ];
    }
    return null;
}

// Função para redirecionar usuário baseado no tipo
function redirectUserByType() {
    if (isAdmin()) {
        header('Location: painel/index.php');
        exit;
    } elseif (isComum()) {
        header('Location: index.php');
        exit;
    }
}