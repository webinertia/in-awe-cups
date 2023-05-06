<?php

declare(strict_types=1);

namespace User\Form\Element\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Element\UserId;
use User\Service\UserServiceInterface;

class UserIdFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserId
    {
        return new $requestedName($container->get(UserServiceInterface::class), 'userId', []);
    }
}
