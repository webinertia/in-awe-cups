<?php
namespace Store\Form\Fieldset\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\Fieldset\ProductImages;

class ProductImagesFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $adapter = $container->get(AdapterInterface::class);
        return new ProductImages(new TableGateway('store_images', $adapter));
    }
}
