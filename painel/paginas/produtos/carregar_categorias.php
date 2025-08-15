<?php
require_once("../../../conexao.php");

$query = $pdo->query("SELECT id, nome FROM categoria ORDER BY nome ASC");
$categorias = $query->fetchAll(PDO::FETCH_ASSOC);

$options = '<option value="">Selecione uma categoria</option>';
foreach($categorias as $cat){
    $options .= '<option value="'.$cat['id'].'">'.$cat['nome'].'</option>';
}

echo $options;
?>
