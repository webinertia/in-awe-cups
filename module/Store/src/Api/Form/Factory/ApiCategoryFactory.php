<?php

declare(strict_types=1);

namespace Store\Api\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Api\Form\ApiCategoryForm;
use Store\Model\Category;

class ApiCategoryFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ApiCategoryForm
    {
        return new $requestedName($container->get(Category::class));
    }
}
