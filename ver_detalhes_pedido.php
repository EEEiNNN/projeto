<?php
// Este ficheiro retorna apenas o HTML dos detalhes do pedido para o AJAX

require_once 'conexao.php';

if (!isLoggedIn()) {
    echo '<p>Sessão expirada. Por favor, faça o login novamente.</p>';
    exit;
}

$pedido_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['id'];

if (!$pedido_id) {
    echo '<p>ID do pedido inválido.</p>';
    exit;
}

// [CORREÇÃO] A query foi ajustada para usar os nomes de tabela e coluna corretos:
// 'itempedidos' em vez de 'itempedido'
// 'pedidos' em vez de 'pedido'
// 'ip.pedidos_id' em vez de 'ip.pedido_id'
$stmt = $pdo->prepare("
    SELECT 
        ip.quantidade, ip.preco,
        pr.nome as nome_produto,
        pe.total as total_pedido,
        pe.status
    FROM itempedidos ip
    JOIN produto pr ON ip.produto_id = pr.id
    JOIN pedidos pe ON ip.pedidos_id = pe.id
    WHERE ip.pedidos_id = ? AND pe.usuario_id = ?
");
$stmt->execute([$pedido_id, $user_id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($itens)) {
    echo '<p>Pedido não encontrado ou não pertence a este utilizador.</p>';
    exit;
}

// O HTML para ser injetado no modal (sem alterações)
?>
<h4>Detalhes do Pedido #<?php echo str_pad($pedido_id, 6, '0', STR_PAD_LEFT); ?></h4>
<p>Status: <strong><?php echo ucfirst($itens[0]['status']); ?></strong></p>
<hr>
<h5>Itens:</h5>
<ul>
    <?php foreach($itens as $item): ?>
        <li>
            <?php echo $item['quantidade']; ?>x 
            <strong><?php echo htmlspecialchars($item['nome_produto']); ?></strong>
            (R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?> cada)
        </li>
    <?php endforeach; ?>
</ul>
<hr>
<h5 style="text-align: right;">Total do Pedido: R$ <?php echo number_format($itens[0]['total_pedido'], 2, ',', '.'); ?></h5>