<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Listener\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\Listener\ProductsListener;
use Store\Model\ProductByCategory;

class ProductsListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProductsListener
    {
        return new $requestedName($container->get(ProductByCategory::class));
    }
}
