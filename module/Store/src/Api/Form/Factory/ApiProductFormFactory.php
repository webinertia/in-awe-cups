<?php

declare(strict_types=1);

namespace Store\Api\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Api\Form\ApiProductForm;
use Store\Model\Category;

final class ApiProductFormFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ApiProductForm
    {
        return new $requestedName(
            $container->get(Category::class),
            $container->get('config'),
            null,
            null
        );
    }
}
