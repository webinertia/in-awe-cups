<?php

declare(strict_types=1);

namespace App\Log;

use App\Log\LogListener;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LogListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LogListener
    {
        return new $requestedName(
            $container->get(LoggerInterface::class),
            $container->get(Translator::class),
            $container->get('config')['app_settings']
        );
    }
}
