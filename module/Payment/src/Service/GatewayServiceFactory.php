<?php

declare(strict_types=1);

namespace Payment\Service;

use Braintree\Gateway as ProviderGateway;
use Laminas\Config\Exception\UnprocessableConfigException as ConfigException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Payment\Service\Gateway;
use Psr\Container\ContainerInterface;

use function extension_loaded;

final class GatewayServiceFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Gateway
    {
        $config = $container->get('config');
        return new $requestedName(
            $container->get(ProviderGateway::class),
            $container->get(Gateway::SESSION_CONTAINER_NAME),
            $config['module_settings']['payment']['gateways'][ProviderGateway::class],
            $config
        );
    }
}
