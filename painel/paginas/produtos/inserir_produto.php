<?php
require_once("../../../conexao.php");

$nome = $_POST['nome'];
$preco = $_POST['preco'];
$categoria_id = $_POST['categoria_id'];
$descricao = $_POST['descricao'];
$imagem = $_POST['imagem'];
$principal = $_POST['principal'];
$ativo = $_POST['ativo'];
$estoque = $_POST['estoque'];
$id = $_POST['id'] ?? '';

// Valida campos obrigatórios
if ($nome == '' || $preco == '' || $categoria_id == '') {
    echo "Preencha todos os campos obrigatórios!";
    exit();
}

if ($id == '') {
    try {
        // INSERE O PRODUTO
        $stmt = $pdo->prepare("INSERT INTO produto (nome, preco, categoria_id, descricao, estoque, data_cadastro, ativo) 
            VALUES (:nome, :preco, :categoria_id, :descricao, :estoque, NOW(), :ativo)");
        $stmt->execute([
            ':nome' => $nome,
            ':preco' => $preco,
            ':categoria_id' => $categoria_id,
            ':descricao' => $descricao,
            ':ativo' => $ativo,
            ':estoque' => $estoque
        ]);

        // PEGAR O ID DO PRODUTO INSERIDO
        $produto_id = $pdo->lastInsertId();

        // INSERE A IMAGEM NA TABELA DE IMAGENS (assumindo que a tabela se chama produto_imagens)
        if (!empty($imagem)) {
            $stmtImg = $pdo->prepare("INSERT INTO imagemproduto (produto_id, url_imagem, principal) VALUES (:produto_id, :imagem, :principal)");
            $stmtImg->execute([
                ':principal' => $principal, 
                ':produto_id' => $produto_id,
                ':imagem' => $imagem
            ]);
        }

        echo "Produto cadastrado com sucesso!";
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    // ATUALIZA O PRODUTO
    $query = $pdo->prepare("UPDATE produto 
        SET nome = :nome, preco = :preco, categoria_id = :categoria_id, descricao = :descricao, estoque = :estoque, ativo = :ativo  
        WHERE id = :id");

    $query->bindValue(":id", $id);
    $query->bindValue(":nome", $nome);
    $query->bindValue(":preco", $preco);
    $query->bindValue(":categoria_id", $categoria_id);
    $query->bindValue(":descricao", $descricao);
    $query->bindValue(":estoque", $estoque);
    $query->bindValue(":ativo", $ativo);

    $query->execute();

    // SE FOI ENVIADA UMA NOVA IMAGEM, ATUALIZA NA TABELA produto_imagens
    if (!empty($imagem)) {
        $stmtImg = $pdo->prepare("INSERT INTO produto_imagens (produto_id, imagem) VALUES (:produto_id, :imagem)");
        $stmtImg->execute([
            ':produto_id' => $id,
            ':imagem' => $imagem
        ]);
    }

    echo "Produto atualizado com sucesso!";
}
?>
