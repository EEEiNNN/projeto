<?php
include 'header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// [CORREÇÃO] A query foi totalmente reescrita para usar as tabelas e colunas corretas
$stmt = $pdo->prepare("
    SELECT 
        p.id, p.total, p.status, p.data_pedidos,
        COUNT(ip.id) as total_itens,
        GROUP_CONCAT(pr.nome SEPARATOR ', ') as produtos,
        CONCAT(e.rua, ', ', e.numero, ' - ', e.cidade, '/', e.estado) as endereco_completo
    FROM pedidos p
    LEFT JOIN itempedidos ip ON p.id = ip.pedidos_id
    LEFT JOIN produto pr ON ip.produto_id = pr.id
    LEFT JOIN endereco e ON p.endereco_id = e.id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.data_pedidos DESC
");
$stmt->execute([$user_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// As funções getStatusColor e getStatusIcon permanecem as mesmas
function getStatusColor($status) {
    switch($status) {
        case 'pendente': return '#f39c12';
        case 'processando': return '#3498db';
        case 'enviado': return '#9b59b6';
        case 'entregue': return '#27ae60';
        case 'cancelado': return '#e74c3c';
        default: return '#95a5a6';
    }
}

function getStatusIcon($status) {
    switch($status) {
        case 'pendente': return 'ri-time-line';
        case 'processando': return 'ri-settings-5-line';
        case 'enviado': return 'ri-truck-line';
        case 'entregue': return 'ri-check-double-line';
        case 'cancelado': return 'ri-close-circle-line';
        default: return 'ri-information-line';
    }
}
?>

<title>Meus Pedidos | Ben-David</title>
<link rel="stylesheet" href="_css/pedidos.css" />

<main>
    <section class="pedidos-section">
        <div class="section__container">
            <h1 class="section__header">Meus Pedidos</h1>
            
            <?php if (empty($pedidos)): ?>
                <div class="pedidos-vazio">
                    <i class="ri-shopping-bag-line"></i>
                    <h3>Você ainda não fez nenhum pedido</h3>
                    <p>Que tal dar uma olhada em nossos produtos?</p>
                    <a href="produtos.php" class="btn">Ver Produtos</a>
                </div>
            <?php else: ?>
                <div class="pedidos-lista">
                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="pedido-card">
                            <div class="pedido-header">
                                <div class="pedido-numero">
                                    <h3>Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></h3>
                                    <span class="pedido-data"><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedidos'])); ?></span>
                                </div>
                                <div class="pedido-status">
                                    <span class="status-badge" style="background-color: <?php echo getStatusColor($pedido['status']); ?>">
                                        <i class="<?php echo getStatusIcon($pedido['status']); ?>"></i>
                                        <?php echo ucfirst($pedido['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="pedido-content">
                                <div class="pedido-info">
                                    <div class="info-item">
                                        <span class="label">Produtos Resumo:</span>
                                        <span class="value"><?php echo htmlspecialchars($pedido['produtos']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Total de itens:</span>
                                        <span class="value"><?php echo $pedido['total_itens']; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Entregar em:</span>
                                        <span class="value"><?php echo htmlspecialchars($pedido['endereco_completo']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="pedido-total">
                                    <span class="total-label">Total:</span>
                                    <span class="total-value">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <div class="pedido-actions">
                                <button class="btn-detalhes" onclick="verDetalhes(<?php echo $pedido['id']; ?>)">
                                    <i class="ri-eye-line"></i> Ver Detalhes
                                </button>
                                </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="modal" id="detalhes-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="fecharModal()">&times;</span>
            <div id="detalhes-content">
                </div>
        </div>
    </div>
</main>

<script src="_js/pedidos.js"></script>

<?php include 'footer.php'; ?>