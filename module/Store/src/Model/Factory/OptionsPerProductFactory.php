<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\OptionsPerProduct;
use Store\Model\ProductOptions;

class OptionsPerProductFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): OptionsPerProduct
    {
        return new $requestedName(
            $container->get(ProductOptions::class),
            new TableGateway(
                $container->get('config')['db']['store_options_per_product_table_name'],
                $container->get(AdapterInterface::class)
            ),
            $container->get('config'),
            $container->get(AdapterPluginManager::class)
        );
    }
}
