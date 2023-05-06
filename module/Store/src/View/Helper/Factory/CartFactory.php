<?php

declare(strict_types=1);

namespace Store\View\Helper\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\Cart as Model;
use Store\View\Helper\Cart;

final class CartFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Cart
    {
        return new $requestedName($container->get(Model::class));
    }
}
