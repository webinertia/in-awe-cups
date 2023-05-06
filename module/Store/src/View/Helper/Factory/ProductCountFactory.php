<?php

declare(strict_types=1);

namespace Store\View\Helper\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\OptionsPerProduct;
use Store\View\Helper\ProductCount;

class ProductCountFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProductCount
    {
        return new $requestedName($container->get(OptionsPerProduct::class));
    }
}
