<?php

declare(strict_types=1);

namespace Store\Form\Element\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\Element\OptionCheckbox;
use Store\Model\OptionsPerProduct;
use Store\Model\ProductOptions;

final class OptionCheckboxFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): OptionCheckbox
    {
        return new $requestedName($container->get(OptionsPerProduct::class), $container->get(ProductOptions::class), null, $options);
    }
}
