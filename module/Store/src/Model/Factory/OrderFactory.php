<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\Order;

final class OrderFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Order
    {
        $config = $container->get('config');
        return new $requestedName(
            new TableGateway(
                $config['db']['store_order_table_name'],
                $container->get(AdapterInterface::class)
            ),
            $config
        );
    }
}
