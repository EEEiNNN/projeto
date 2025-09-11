<?php
// buscar_detalhes.php
require_once '../../../conexao.php';

// Inicia a sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o utilizador é admin
if ($_SESSION['nivel'] != 'admin') {
    echo '<h4>Acesso negado.</h4>';
    exit();
}

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? 0;

if (!$id || !$tipo) {
    echo '<h4>Dados inválidos.</h4>';
    exit();
}

switch ($tipo) {
    case 'usuario':
        $stmt = $pdo->prepare("SELECT u.*, e.cep, e.rua, e.numero, e.bairro, e.cidade, e.estado 
                               FROM usuarios u 
                               LEFT JOIN endereco e ON u.endereco_id = e.id 
                               WHERE u.id = ?");
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            echo "<h4>Detalhes do Utilizador: #{$dados['id']}</h4>";
            echo "<p><strong>Nome:</strong> " . htmlspecialchars($dados['nome']) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($dados['email']) . "</p>";
            echo "<p><strong>Telefone:</strong> " . htmlspecialchars($dados['telefone']) . "</p>";
            echo "<p><strong>Nível:</strong> " . ucfirst($dados['nivel']) . "</p>";
            echo "<p><strong>Status:</strong> " . ucfirst($dados['status']) . "</p>";
            echo "<p><strong>Ativo:</strong> " . $dados['ativo'] . "</p>";
            echo "<p><strong>Data de Registo:</strong> " . date('d/m/Y H:i', strtotime($dados['data'])) . "</p>";
            if ($dados['cep']) {
                echo "<hr><h5>Endereço Principal</h5>";
                echo "<p>" . htmlspecialchars($dados['rua']) . ", " . htmlspecialchars($dados['numero']) . "</p>";
                echo "<p>" . htmlspecialchars($dados['bairro']) . " - " . htmlspecialchars($dados['cidade']) . "/" . htmlspecialchars($dados['estado']) . "</p>";
                echo "<p><strong>CEP:</strong> " . htmlspecialchars($dados['cep']) . "</p>";
            }
        } else {
            echo "<h4>Utilizador não encontrado.</h4>";
        }
        break;

    case 'produto':
        $stmt = $pdo->prepare("SELECT p.*, c.nome as nome_categoria 
                               FROM produto p 
                               LEFT JOIN categoria c ON p.categoria_id = c.id 
                               WHERE p.id = ?");
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            echo "<h4>Detalhes do Produto: #{$dados['id']}</h4>";
            echo "<p><strong>Nome:</strong> " . htmlspecialchars($dados['nome']) . "</p>";
            echo "<p><strong>Descrição:</strong> " . nl2br(htmlspecialchars($dados['descricao'])) . "</p>";
            echo "<p><strong>Preço:</strong> R$ " . number_format($dados['preco'], 2, ',', '.') . "</p>";
            echo "<p><strong>Stock:</strong> " . $dados['estoque'] . " unidades</p>";
            echo "<p><strong>Categoria:</strong> " . htmlspecialchars($dados['nome_categoria']) . "</p>";
            echo "<p><strong>Ativo:</strong> " . ($dados['ativo'] ? 'Sim' : 'Não') . "</p>";
            echo "<p><strong>Data de Registo:</strong> " . date('d/m/Y H:i', strtotime($dados['data_cadastro'])) . "</p>";
        } else {
            echo "<h4>Produto não encontrado.</h4>";
        }
        break;

    case 'pedido':
        $stmt = $pdo->prepare("SELECT p.*, u.nome as nome_cliente, e.rua, e.numero, e.cidade, e.estado, e.cep
                               FROM pedidos p
                               JOIN usuarios u ON p.usuario_id = u.id
                               LEFT JOIN endereco e ON p.endereco_id = e.id
                               WHERE p.id = ?");
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            $stmt_itens = $pdo->prepare("SELECT i.*, pr.nome as nome_produto 
                                         FROM itempedidos i
                                         JOIN produto pr ON i.produto_id = pr.id
                                         WHERE i.pedidos_id = ?");
            $stmt_itens->execute([$id]);
            $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

            echo "<h4>Detalhes do Pedido: #{$pedido['id']}</h4>";
            echo "<p><strong>Cliente:</strong> " . htmlspecialchars($pedido['nome_cliente']) . "</p>";
            echo "<p><strong>Data:</strong> " . date('d/m/Y H:i', strtotime($pedido['data_pedidos'])) . "</p>";
            echo "<p><strong>Status:</strong> <span class='label label-info'>" . ucfirst($pedido['status']) . "</span></p>";
            echo "<p><strong>Total:</strong> R$ " . number_format($pedido['total'], 2, ',', '.') . "</p>";
            
            if ($pedido['rua']) {
                echo "<hr><h5>Endereço de Entrega</h5>";
                echo "<p>" . htmlspecialchars($pedido['rua']) . ", " . htmlspecialchars($pedido['numero']) . "</p>";
                echo "<p>" . htmlspecialchars($pedido['cidade']) . "/" . htmlspecialchars($pedido['estado']) . " - CEP: " . htmlspecialchars($pedido['cep']) . "</p>";
            }

            echo "<hr><h5>Itens do Pedido</h5>";
            if ($itens) {
                echo "<ul>";
                foreach ($itens as $item) {
                    echo "<li>" . $item['quantidade'] . "x " . htmlspecialchars($item['nome_produto']) . " (R$ " . number_format($item['preco'], 2, ',', '.') . ")</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Nenhum item encontrado para este pedido.</p>";
            }

        } else {
            echo "<h4>Pedido não encontrado.</h4>";
        }
        break;

    default:
        echo "<h4>Tipo de dado não reconhecido.</h4>";
        break;
}
?>

