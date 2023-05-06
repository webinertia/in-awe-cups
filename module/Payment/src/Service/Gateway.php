<?php

declare(strict_types=1);

namespace Payment\Service;

use Braintree\Gateway as ProviderGateway;
use Laminas\Session\Container;

final class Gateway
{
    public const SESSION_CONTAINER_NAME = 'PaymentGateway';
    protected Container $container;
    protected ProviderGateway $gateway;
    protected array $settings;
    protected array $config;

    public function __construct(ProviderGateway $gateway, Container $container, array $settings, array $config)
    {
        $this->gateway   = $gateway;
        $this->container = $container;
        $this->settings  = $settings;
        $this->config    = $config;
    }

    public function getIntegrationType(): string
    {
        $this->settings['integration_type'];
        switch (true) {
            case $this->settings['integration_type']['hosted_fields']:
                return 'hosted_fields';
            case $this->settings['integration_type']['drop_in']:
                return 'drop_in';
            break;
        }
    }

    public function getSessionContainer(): Container
    {
        return $this->container;
    }

    public function __call($name, $arguments)
    {
        return $this->gateway->$name(...$arguments);
    }
}
