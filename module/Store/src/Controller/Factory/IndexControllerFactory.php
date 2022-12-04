<?php

declare(strict_types=1);

namespace Store\Controller\Factory;

use App\Controller\Factory\AbstractControllerFactory;
use Psr\Container\ContainerInterface;
use Store\Controller\IndexController;
use Store\Model\Cart;

class IndexControllerFactory extends AbstractControllerFactory
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): IndexController
    {
        //return new $requestedName($container->get(Cart::class));
        $controller = parent::__invoke($container, $requestedName, $options);
        $controller->setCart($container->get(Cart::class));
        return $controller;
    }
}
