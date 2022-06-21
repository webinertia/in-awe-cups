<?php

/** This factory can be used to create the majority of controllers */

declare(strict_types=1);

namespace App\Controller\Factory;

use App\Model\Settings;
use Laminas\Config\Config;
use Laminas\Form\FormElementManager;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stdlib\DispatchableInterface;
use Psr\Container\ContainerInterface;
use Webinertia\ModelManager\ModelManager;

class AppControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): DispatchableInterface {
        return new $requestedName(
            $container,
            $container->get('Request'),
            new Config($container->get('config')),
            $container->get(FormElementManager::class),
            $container->get(Logger::class),
            $container->get(ModelManager::class),
            $container->get(ModelManager::class)->get(Settings::class)
        );
    }
}
