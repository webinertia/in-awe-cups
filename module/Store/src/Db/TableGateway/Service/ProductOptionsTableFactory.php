<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ProductOptionsTable;
use Store\Model\ProductOptions;

class ProductOptionsTableFactory implements FactoryInterface
{
        /** @inheritDoc */
        public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProductOptionsTable
        {
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ProductOptions());
            return new $requestedName(
                'store_product_options',
                $container->get('EventManager'),
                $resultSetPrototype,
                false,
                null,
                $container->get(AdapterInterface::class)
            );
        }
}
