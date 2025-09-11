<?php
// --- Início do Bloco PHP para buscar dados do Banco ---

// Contagem total de usuários
$query_usuarios = $pdo->query("SELECT COUNT(id) as total FROM usuarios");
$res_usuarios = $query_usuarios->fetch(PDO::FETCH_ASSOC);
$total_usuarios = $res_usuarios['total'];

// Contagem total de pedidos
$query_pedidos = $pdo->query("SELECT COUNT(id) as total FROM pedidos");
$res_pedidos = $query_pedidos->fetch(PDO::FETCH_ASSOC);
$total_pedidos = $res_pedidos['total'];

// Faturamento total (soma dos pedidos com status 'entregue' ou 'processando')
$query_faturamento = $pdo->query("SELECT SUM(total) as total FROM pedidos WHERE status IN ('entregue', 'processando')");
$res_faturamento = $query_faturamento->fetch(PDO::FETCH_ASSOC);
$faturamento_total = $res_faturamento['total'] ?? 0; // Se for nulo, assume 0

// Contagem total de produtos
$query_produtos = $pdo->query("SELECT COUNT(id) as total FROM produto");
$res_produtos = $query_produtos->fetch(PDO::FETCH_ASSOC);
$total_produtos = $res_produtos['total'];

// Pedidos recentes para a tabela
$query_recentes = $pdo->query("
    SELECT p.id, p.total, p.status, u.nome as nome_cliente 
    FROM pedidos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    ORDER BY p.data_pedidos DESC 
    LIMIT 5
");
$pedidos_recentes = $query_recentes->fetchAll(PDO::FETCH_ASSOC);

// Dados para o gráfico de vendas dos últimos 7 dias
$query_vendas_semana = $pdo->query("
    SELECT 
        DATE(data_pedidos) as dia, 
        SUM(total) as total_dia 
    FROM pedidos 
    WHERE data_pedidos >= CURDATE() - INTERVAL 7 DAY 
    GROUP BY DATE(data_pedidos) 
    ORDER BY dia ASC
");
$vendas_semana = $query_vendas_semana->fetchAll(PDO::FETCH_ASSOC);

// Formata os dados para o JavaScript do gráfico
$grafico_data = [];
$dias_semana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

// Inicializa os últimos 7 dias com valor 0
for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i days"));
    $dia_semana_nome = $dias_semana[date('w', strtotime($data))];
    $grafico_data[$dia_semana_nome] = ['X' => $dia_semana_nome, 'Y' => 0];
}

foreach ($vendas_semana as $venda) {
    $dia_semana_nome = $dias_semana[date('w', strtotime($venda['dia']))];
    $grafico_data[$dia_semana_nome]['Y'] = (float)$venda['total_dia'];
}

$vendas_semana_json = json_encode(array_values($grafico_data));

// --- Fim do Bloco PHP ---
?>

<div class="main-page">
    <div class="col_3">
        <div class="col-md-3 widget widget1">
            <div class="r3_counter_box" style="background-color: #251B18;">
                <i class="pull-left fa fa-dollar icon-rounded" style="background-color: #A1CCA5;"></i>
                <div class="stats">
                    <h5 style="color: #A1CCA5;"><strong>R$ <?php echo number_format($faturamento_total, 2, ',', '.'); ?></strong></h5>
                    <span style="color: #709775;">Faturamento Total</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 widget widget1">
            <div class="r3_counter_box"style="background-color: #251B18;">
                <i class="pull-left fa fa-shopping-cart user1 icon-rounded" style="background-color: #A1CCA5;"></i>
                <div class="stats">
                    <h5 style="color: #A1CCA5;"><strong><?php echo $total_pedidos; ?></strong></h5>
                    <span style="color: #709775;">Total de Pedidos</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 widget widget1">
            <div class="r3_counter_box" style="background-color: #251B18;">
                <i class="pull-left fa fa-users user2 icon-rounded" style="background-color: #A1CCA5;"></i>
                <div class="stats">
                    <h5 style="color: #A1CCA5;"><strong><?php echo $total_usuarios; ?></strong></h5>
                    <span style="color: #709775;">Total de Clientes</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 widget">
            <div class="r3_counter_box" style="background-color: #251B18;">
                <i class="pull-left fa fa-archive dollar2 icon-rounded" style="background-color: #A1CCA5;"></i>
                <div class="stats">
                    <h5 style="color: #A1CCA5;"><strong><?php echo $total_produtos; ?></strong></h5>
                    <span style="color: #709775;">Produtos Cadastrados</span>
                </div>
            </div>
        </div>
        <div class="clearfix"> </div>
    </div>
    
    <div class="row-one widgettable">
        <div class="col-md-8 content-top-2 card" style="background-color: #251B18;">
            <div class="agileinfo-cdr">
                <div class="card-header">
                    <h3 style="color: #A1CCA5;">Vendas da Semana</h3>
                </div>
                <div id="Linegraph" style="width: 98%; height: 350px"></div>
            </div>
        </div>
        
        <div class="col-md-4 stat" >
             <div class="card" >
                <div class="card-header">
                    <h3>Pedidos Recentes</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos_recentes as $pedido): ?>
                                <tr>
                                    <td><?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['nome_cliente']); ?></td>
                                    <td><span class="label label-info"><?php echo ucfirst($pedido['status']); ?></span></td>
                                    <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"> </div>
    </div>
</div>

<script src="js/SimpleChart.js"></script>
<script>
	
    // Pega os dados de vendas da semana gerados pelo PHP
    var vendasSemanaData = {
        linecolor: "#709775",
        title: "Vendas",
        values: <?php echo $vendas_semana_json; ?>
    };

    $(function () {
        // Inicializa o gráfico de linha com os dados dinâmicos
        $("#Linegraph").SimpleChart({
            ChartType: "Line",
            toolwidth: "50",
            toolheight: "25",
            axiscolor: "#FDF9F9",
            textcolor: "#A1CCA5",
            showlegends: false,
            data: [vendasSemanaData],
            xaxislabel: 'Dias da Semana',
            title: 'Resumo de Vendas',
            yaxislabel: 'Valor (R$)'
        });
    });
</script>