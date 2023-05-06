<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Listener\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\Listener\CategoriesListener;
use Store\Model\ProductOptions;

final class CategoriesListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CategoriesListener
    {
        return new $requestedName($container->get(ProductOptions::class));
    }
}
