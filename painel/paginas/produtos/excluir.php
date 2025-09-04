<?php
require_once("../../../conexao.php");

$id = $_POST['id'] ?? '';

if($id == '') {
    echo "ID não recebido";
    exit();
}

try {
    $query = $pdo->prepare("DELETE FROM produto WHERE  id = :id");
    $query->bindValue(":id", $id);
    $query->execute();
    echo "Excluído com Sucesso";
} catch (Exception $e) {
    echo "Erro ao excluir: " . $e->getMessage();
}
?>