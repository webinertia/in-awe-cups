<?php

declare(strict_types=1);

namespace Store\Model;

use Store\Model\Cart;

trait CartAwareTrait
{
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
