<?php

declare(strict_types=1);

namespace Store\Form\Fieldset\Factory;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\Fieldset\CategoryInfo;
use Store\Model\Category;

final class CategoryInfoFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CategoryInfo
    {
        if ($options === null) {
            $options = $container->get('config');
        } else {
            $options += $container->get('config');
        }
        return new $requestedName($container->get(Category::class), 'category-data', $options);
    }
}
