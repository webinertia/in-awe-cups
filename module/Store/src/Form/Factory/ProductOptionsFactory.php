<?php

declare(strict_types=1);

namespace Store\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\ProductOptions as Form;

class ProductOptionsFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Form
    {
        return new $requestedName('optionsForm', $options);
    }
}
