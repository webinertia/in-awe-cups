<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ProductsTable;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Model\Product;

final class ProductFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Product
    {
        return new $requestedName(
            $container->get(ProductsTable::class),
            $container->get(ProductsByCategoryTable::class)
        );
    }
}