<?php
// Inicia a sessão de forma segura
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php'; // Inclui a conexão e a função isLoggedIn()

// --- PASSO 1: VERIFICAÇÕES DE SEGURANÇA ---

// Garante que o utilizador está logado e que o pedido é do tipo POST
if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$endereco_id = $_POST['endereco_id'] ?? null;

// Busca o carrinho_id
$stmtCart = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
$stmtCart->execute([$user_id]);
$carrinho = $stmtCart->fetch();
$carrinho_id = $carrinho ? $carrinho['id'] : 0;

// Busca os itens do carrinho para processamento
$stmtItens = $pdo->prepare("SELECT * FROM itemcarrinho WHERE carrinho_id = ?");
$stmtItens->execute([$carrinho_id]);
$itens_carrinho = $stmtItens->fetchAll();

// Se o carrinho estiver vazio, não há nada a fazer, volta para a página inicial
if (empty($itens_carrinho)) {
    header('Location: index.php');
    exit;
}

try {
    // --- PASSO 2: INICIA A TRANSAÇÃO ---
    // A partir daqui, ou tudo funciona, ou nada é salvo no banco de dados.
    $pdo->beginTransaction();

    // --- PASSO 3: CALCULAR O TOTAL FINAL NO SERVIDOR ---
    // (Nunca confiar no total enviado pelo cliente)
    $total_final = 0;
    $stmtPreco = $pdo->prepare("SELECT preco FROM produto WHERE id = ?");
    foreach($itens_carrinho as $item) {
        $stmtPreco->execute([$item['produto_id']]);
        $produto = $stmtPreco->fetch();
        if ($produto) {
            $total_final += $produto['preco'] * $item['quantidade'];
        }
    }

    // --- PASSO 4: CRIA O REGISTO NA TABELA `pedido` ---
    $stmtPedido = $pdo->prepare(
        "INSERT INTO pedido (usuario_id, total, data_pedido, status, endereco_id) 
         VALUES (?, ?, NOW(), 'pendente', ?)"
    );
    // Usamos o endereco_id do seu banco de dados
    $stmtPedido->execute([$user_id, $total_final, $endereco_id]);
    $pedido_id = $pdo->lastInsertId(); // Pega o ID do pedido que acabámos de criar

    // --- PASSO 5: MOVE OS ITENS DO CARRINHO PARA A TABELA `itempedido` ---
    // Para cada item no carrinho, cria um registo correspondente em 'itempedido'
    $stmtMoverItem = $pdo->prepare(
        "INSERT INTO itempedido (pedido_id, produto_id, quantidade, preco) 
         VALUES (:pedido_id, :produto_id, :quantidade, :preco)"
    );

    foreach ($itens_carrinho as $item) {
        // Busca o preço atual do produto para garantir consistência
        $stmtPreco->execute([$item['produto_id']]);
        $produto = $stmtPreco->fetch();
        $preco_atual = $produto['preco'] ?? 0;

        $stmtMoverItem->execute([
            ':pedido_id' => $pedido_id,
            ':produto_id' => $item['produto_id'],
            ':quantidade' => $item['quantidade'],
            ':preco' => $preco_atual
        ]);
    }
    
    // --- PASSO 6: LIMPA O CARRINHO DO UTILIZADOR ---
    $stmtLimpar = $pdo->prepare("DELETE FROM itemcarrinho WHERE carrinho_id = ?");
    $stmtLimpar->execute([$carrinho_id]);

    // --- PASSO 7: CONFIRMA A TRANSAÇÃO ---
    // Se todos os passos acima foram executados sem erros, confirma as alterações
    $pdo->commit();
    
    // Redireciona para a página "Meus Pedidos" com uma mensagem de sucesso
    header('Location: meus-pedidos.php?status=sucesso');
    exit;

} catch (Exception $e) {
    // --- PASSO 8: DESFAZ TUDO EM CASO DE ERRO ---
    // Se qualquer um dos passos acima falhar, desfaz todas as operações
    $pdo->rollBack();
    
    // Guarda uma mensagem de erro na sessão e redireciona de volta para o checkout
    $_SESSION['feedback_checkout'] = "Ocorreu um erro ao finalizar o seu pedido. Por favor, tente novamente.";
    
    // Para depuração, pode registar o erro real num ficheiro de log
    // error_log("Erro no checkout: " . $e->getMessage());
    
    header('Location: checkout.php');
    exit;
}
?>