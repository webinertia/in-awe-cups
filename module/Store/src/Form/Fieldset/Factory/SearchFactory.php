<?php

declare(strict_types=1);

namespace Store\Form\Fieldset\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Form\Fieldset\SearchFieldset;


class SearchFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SearchFieldset
    {
        return $requestedName('search', $options);
    }
}
