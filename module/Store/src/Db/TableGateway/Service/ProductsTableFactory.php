<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ProductsTable;
use Store\Model\Product;

class ProductsTableFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProductsTable
    {
        $config             = $container->get('config');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(
            new Product()
        );
        return new $requestedName(
            $config['db']['products_table_name'],
            $container->get('EventManager'), // This must use the string name not the class-string
            $resultSetPrototype,
            false,
            null,
            $container->get(AdapterInterface::class)
        );
    }
}
