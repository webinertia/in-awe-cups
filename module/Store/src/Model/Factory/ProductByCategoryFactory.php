<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Model\ProductByCategory;

class ProductByCategoryFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProductByCategory
    {
        return new $requestedName($container->get(ProductsByCategoryTable::class));
    }
}
