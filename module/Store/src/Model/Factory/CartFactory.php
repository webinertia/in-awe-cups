<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\Cart;

class CartFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Cart
    {
        return new $requestedName($container->get('Cart_Context'));
    }
}
