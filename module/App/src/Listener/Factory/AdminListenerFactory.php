<?php

declare(strict_types=1);

namespace App\Listener\Factory;

use App\Listener\AdminListener;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Resolver\TemplateMapResolver;
use Psr\Container\ContainerInterface;

class AdminListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AdminListener
    {
        return new $requestedName(
            $container->get(ControllerManager::class),
            //$container->get(LoggerInterface::class),
            $container->get(TemplateMapResolver::class),
            $container->get(Translator::class),
            $container->get('config')['app_settings']['theme']
        );
    }
}
