<?php

declare(strict_types=1);

namespace InAweCups\View\Helper\Factory;

use InAweCups\View\Helper\CategoryMenu;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\Url;
use Psr\Container\ContainerInterface;
use Store\Model\Category;
use Store\View\Helper\LabelHelper;

class CategoryMenuFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CategoryMenu
    {
        return new $requestedName(
            $container->get(Category::class),
            $container->get('ViewHelperManager')->get(LabelHelper::class),
            $container->get('ViewHelperManager')->get(Url::class)
        );
    }
}
