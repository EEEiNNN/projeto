<?php
include 'header.php';

// Verificar se usuário está logado
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar pedidos do usuário
$stmt = $pdo->prepare("
    SELECT p.*, 
           COUNT(pi.id) as total_itens,
           GROUP_CONCAT(pr.nome SEPARATOR ', ') as produtos
    FROM pedidos p
    LEFT JOIN pedido_itens pi ON p.id = pi.pedido_id
    LEFT JOIN produtos pr ON pi.produto_id = pr.id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.data_pedido DESC
");
$stmt->execute([$user_id]);
$pedidos = $stmt->fetchAll();

// Função para obter cor do status
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

// Função para obter ícone do status
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
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
    <title>Meus Pedidos | Ben-David</title>
</head>
<body>
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
                                    <span class="pedido-data">
                                        <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                                    </span>
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
                                        <span class="label">Produtos:</span>
                                        <span class="value"><?php echo h($pedido['produtos']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Total de itens:</span>
                                        <span class="value"><?php echo $pedido['total_itens']; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Endereço:</span>
                                        <span class="value"><?php echo h($pedido['endereco_entrega']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="pedido-total">
                                    <span class="total-label">Total:</span>
                                    <span class="total-value">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <div class="pedido-actions">
                                <button class="btn-detalhes" onclick="verDetalhes(<?php echo $pedido['id']; ?>)">
                                    <i class="ri-eye-line"></i>
                                    Ver Detalhes
                                </button>
                                
                                <?php if ($pedido['status'] == 'pendente'): ?>
                                    <button class="btn-cancelar" onclick="cancelarPedido(<?php echo $pedido['id']; ?>)">
                                        <i class="ri-close-line"></i>
                                        Cancelar
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($pedido['status'] == 'entregue'): ?>
                                    <button class="btn-recomprar" onclick="recomprar(<?php echo $pedido['id']; ?>)">
                                        <i class="ri-repeat-line"></i>
                                        Comprar Novamente
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal de Detalhes -->
    <div class="modal" id="detalhes-modal">
        <div class="modal-content modal-large">
            <span class="close-btn" onclick="fecharModal()">&times;</span>
            <div id="detalhes-content">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script src="js/pedidos.js"></script>
<?php include 'footer.php'; ?>