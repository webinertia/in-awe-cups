<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\Listener\ProductsByCategoryListener;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Model\ProductByCategory;

class ProductsByCategoryTableFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = NULL)
    {
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new ProductByCategory());
        return new ProductsByCategoryTable(
            'store_products_by_category_lookup',
            $container->get('EventManager'),
            $resultSet,
            true,
            null,
            $container->get(AdapterInterface::class)
        );
    }
}
