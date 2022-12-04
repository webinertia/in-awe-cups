<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\CategoriesTable;
use Store\Db\TableGateway\Listener\CategoriesListener;
use Store\Model\Category;

class CategoriesTableFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CategoriesTable
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Category(null, new Factory($container->get(InputFilterPluginManager::class))));
        return new $requestedName(
            'store_categories',
            $container->get('EventManager'),
            $resultSetPrototype,
            true,
            $container->get(CategoriesListener::class),
            $container->get(AdapterInterface::class)
        );
    }
}
