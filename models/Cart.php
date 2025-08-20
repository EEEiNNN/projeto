<?php

class Cart {
    private $items = [];

    public function addProduct($product, $quantity = 1) {
        if (isset($this->items[$product->id])) {
            $this->items[$product->id]['quantity'] += $quantity;
        } else {
            $this->items[$product->id] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
    }

    public function removeProduct($productId) {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);
        }
    }

    public function getItems() {
        return $this->items;
    }

    public function calculateTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['product']->price * $item['quantity'];
        }
        return $total;
    }
    public function clear() {
        $this->items = [];
    }

    public function getTotalPrice() {
        return $this->calculateTotal();
    }
}