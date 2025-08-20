<?php
class CartController {
    private $cart;

    public function __construct() {
        $this->cart = new Cart();
    }

    public function addProduct($productId, $quantity) {
        $this->cart->add($productId, $quantity);
    }

    public function removeProduct($productId) {
        $this->cart->remove($productId);
    }

    public function viewCart() {
        return $this->cart->getItems();
    }

    public function getTotal() {
        return $this->cart->getTotalPrice();
    }
}
?>