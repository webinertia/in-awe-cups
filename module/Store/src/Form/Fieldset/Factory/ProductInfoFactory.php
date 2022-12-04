<?php

declare(strict_types=1);

namespace Store\Form\Fieldset\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\Fieldset\ProductInfo;
use Store\Model\Category;

class ProductInfoFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ProductInfo($container->get(Category::class), $container->get('config')['app_settings']);
    }
}