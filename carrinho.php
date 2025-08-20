<?php
session_start();

// Inicializa o carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adiciona produto ao carrinho
if (isset($_POST['produto'], $_POST['quantidade'])) {
    $produto = $_POST['produto'];
    $quantidade = (int)$_POST['quantidade'];

    if ($quantidade > 0) {
        if (isset($_SESSION['carrinho'][$produto])) {
            $_SESSION['carrinho'][$produto] += $quantidade;
        } else {
            $_SESSION['carrinho'][$produto] = $quantidade;
        }
    }
}

// Remove produto do carrinho
if (isset($_GET['remover'])) {
    $produto = $_GET['remover'];
    unset($_SESSION['carrinho'][$produto]);
}

// Lista de produtos fictícios
$produtos = [
    '1' => ['nome' => 'Produto A', 'preco' => 10.00],
    '2' => ['nome' => 'Produto B', 'preco' => 20.00],
    '3' => ['nome' => 'Produto C', 'preco' => 30.00],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
</head>
<body>
    <h1>Produtos</h1>
    <form method="post">
        <select name="produto">
            <?php foreach ($produtos as $id => $produto): ?>
                <option value="<?= $id ?>"><?= $produto['nome'] ?> - R$ <?= number_format($produto['preco'], 2, ',', '.') ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantidade" value="1" min="1" required>
        <button type="submit">Adicionar ao Carrinho</button>
    </form>

    <h2>Carrinho</h2>
    <?php if (empty($_SESSION['carrinho'])): ?>
        <p>O carrinho está vazio.</p>
    <?php else: ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Total</th>
                <th>Ação</th>
            </tr>
            <?php
            $total = 0;
            foreach ($_SESSION['carrinho'] as $id => $qtd):
                $nome = $produtos[$id]['nome'];
                $preco = $produtos[$id]['preco'];
                $subtotal = $preco * $qtd;
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($nome) ?></td>
                <td><?= $qtd ?></td>
                <td>R$ <?= number_format($preco, 2, ',', '.') ?></td>
                <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                <td><a href="?remover=<?= $id ?>">Remover</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td colspan="2"><strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></td>
            </tr>
        </table>
    <?php endif; ?>
</body>
</html>