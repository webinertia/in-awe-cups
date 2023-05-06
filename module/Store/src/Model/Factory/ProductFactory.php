<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Paginator\AdapterPluginManager;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ProductsTable;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Model\Image;
use Store\Model\OptionsPerProduct;
use Store\Model\Product;

final class ProductFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Product
    {
        return new $requestedName(
            $container->get(ProductsTable::class),
            $container->get(ProductsByCategoryTable::class),
            $container->get(OptionsPerProduct::class),
            $container->get(Image::class),
            $container->get('config'),
            $container->get(AdapterPluginManager::class),
            $container->get(FilterPluginManager::class)
        );
    }
}