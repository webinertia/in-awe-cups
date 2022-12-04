<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\OrdersTable;


class OrdersTableFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = NULL)
    {
        return new OrdersTable('store_orders', $container);
    }
}