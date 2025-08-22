<?php
require_once("../../../conexao.php");
$tabela = 'pedidos';

$query = $pdo->query("SELECT p.*, u.nome as nome_cliente FROM $tabela p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = count($res);
if ($linhas > 0) {
    echo <<<HTML
    <small>
    <table class="table table-hover" id="tabela">
    <thead> <tr> <th>ID</th> <th>Cliente</th> <th>Total</th> <th>Status</th> <th>Data</th> <th>Ações</th> </tr> </thead>
    <tbody>
    HTML;
    foreach ($res as $item) {
        $id = $item['id'];
        $cliente = htmlspecialchars($item['nome_cliente']);
        $total = number_format($item['total'], 2, ',', '.');
        $status = ucfirst($item['status']);
        $data = date('d/m/Y', strtotime($item['data_pedidos']));
        
        echo <<<HTML
        <tr>
        <td>#{$id}</td>
        <td>{$cliente}</td>
        <td>R$ {$total}</td>
        <td>{$status}</td>
        <td>{$data}</td>
        <td>
            <big><a href="#" onclick="editar('{$id}')" title="Editar Pedido"><i class="fa fa-edit text-primary"></i></a></big>
            <big><a href="#" onclick="excluir('{$id}')" title="Excluir Pedido"><i class="fa fa-trash-o text-danger"></i></a></big>
        </td>
        </tr>
        HTML;
    }
    echo <<<HTML
    </tbody> </table> </small>
    HTML;
} else {
    echo "Nenhum pedido encontrado.";
}
?>