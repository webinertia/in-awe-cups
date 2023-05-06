<?php

declare(strict_types=1);

namespace App\Upload;

use App\Upload\UploadListener;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Service\UserServiceInterface;

class UploadListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UploadListener
    {
        return new $requestedName($container->get(UserServiceInterface::class));
    }
}
