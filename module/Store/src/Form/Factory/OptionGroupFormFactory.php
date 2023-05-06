<?php

declare(strict_types=1);

namespace Store\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\OptionGroupForm;
use Store\Model\Category;

class OptionGroupFormFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): OptionGroupForm
    {
        return new $requestedName($container->get(Category::class), null, $options);
    }
}
