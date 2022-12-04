<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterPluginManager;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\CategoriesTable;
use Store\Model\Category;

class CategoryFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Category
    {
        $filterFactory = new Factory($container->get(InputFilterPluginManager::class));
        return new $requestedName($container->get(CategoriesTable::class), $filterFactory);
    }
}
