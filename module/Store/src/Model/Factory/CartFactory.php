<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\Cart;
use Store\Model\OptionsPerProduct;
use Store\Model\Order;
use Store\Model\Product;

class CartFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Cart
    {
        return new $requestedName(
            $container->get('Cart'),
            $container->get(OptionsPerProduct::class),
            $container->get(Order::class),
            $container->get(Product::class),
        );
    }
}
