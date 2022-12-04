<?php
declare(strict_types=1);
namespace Store\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Store\Controller\ShippingController;

class ShippingControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = NULL)
    {
        $acl = $container->get('Acl');
        $shippingManager = $container->get('Store\Shipping\ShippingManager');
        return new ShippingController($acl, $shippingManager);
    }
}