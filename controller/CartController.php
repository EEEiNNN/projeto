<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Produto.php';

class CartController
{
    private $cart;
    private $pdo; // Adiciona uma propriedade para a conexão com o banco de dados

    public function __construct()
    {
        global $pdo; // Utiliza a conexão global do seu arquivo conexao.php
        $this->pdo = $pdo;
        $this->cart = new Cart();
    }

    /**
     * Adiciona um produto ao carrinho e diminui o estoque.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool|string Retorna true em caso de sucesso ou uma mensagem de erro.
     */
    public function addProduct($productId, $quantity)
    {
        try {
            // Inicia uma transação para garantir que ambas as operações (atualizar estoque e adicionar ao carrinho) ocorram com sucesso.
            $this->pdo->beginTransaction();

            // 1. Busca o produto no banco de dados para verificar o estoque
            $produto = new Produto($this->pdo); // Assumindo que a classe Produto recebe a conexão PDO
            $productDetails = $produto->findById($productId); // Assumindo que existe um método para buscar por ID

            if (!$productDetails || $productDetails['estoque'] < $quantity) {
                // Se o produto não existe ou não há estoque suficiente, desfaz a transação e retorna um erro
                $this->pdo->rollBack();
                return "Produto esgotado ou quantidade indisponível!";
            }

            // 2. Diminui o estoque do produto no banco de dados
            $novoEstoque = $productDetails['estoque'] - $quantity;
            $produto->updateStock($productId, $novoEstoque); // Assumindo que existe um método para atualizar o estoque

            // 3. Adiciona o produto ao carrinho
            $this->cart->add($productId, $quantity);

            // Se tudo correu bem, confirma as alterações no banco de dados
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            // Se ocorrer qualquer erro, desfaz a transação
            $this->pdo->rollBack();
            return "Ocorreu um erro ao adicionar o produto: " . $e->getMessage();
        }
    }

    /**
     * Remove um produto do carrinho e restaura o estoque.
     *
     * @param int $productId
     * @return bool
     */
    public function removeProduct($productId)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Pega a quantidade do item que está no carrinho antes de remover
            $items = $this->cart->getItems();
            $quantityInCart = 0;
            foreach ($items as $item) {
                if ($item['id'] == $productId) {
                    $quantityInCart = $item['quantity'];
                    break;
                }
            }

            if ($quantityInCart > 0) {
                // 2. Restaura o estoque do produto no banco de dados
                $produto = new Produto($this->pdo);
                $productDetails = $produto->findById($productId);
                $novoEstoque = $productDetails['estoque'] + $quantityInCart;
                $produto->updateStock($productId, $novoEstoque);
            }

            // 3. Remove o produto do carrinho
            $this->cart->remove($productId);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Você pode querer logar o erro aqui
            return false;
        }
    }

    public function viewCart()
    {
        return $this->cart->getItems();
    }

    public function getTotal()
    {
        return $this->cart->getTotalPrice();
    }
}
?>