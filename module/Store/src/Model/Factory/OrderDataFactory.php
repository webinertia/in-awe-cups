<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stdlib\ArrayObject;
use Psr\Container\ContainerInterface;
use Store\Model\OrderData;

class OrderDataFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): OrderData
    {
        return new $requestedName([], ArrayObject::ARRAY_AS_PROPS);
    }
}
