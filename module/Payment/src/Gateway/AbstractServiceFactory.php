<?php

namespace Payment\Gateway;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;

use function is_array;
use function extension_loaded;

/**
 * Database adapter abstract service factory.
 *
 * Allows configuring several database instances (such as writer and reader).
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{
    /** @var array */
    protected $config;

    /**
     * Can we create an adapter by the requested name?
     *
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        if (isset($config[$requestedName]) && is_array($config[$requestedName]) && ! empty($config[$requestedName])) {
            if (isset($config[$requestedName]['runtime_check'])) {
                try {
                    foreach ($config[$requestedName]['runtime_check'] as $check) {
                        $runtimeCheck = new $check();
                        $runtimeCheck();
                    }
                } catch (\Throwable $th) {
                    throw new ServiceNotCreatedException($th->getMessage());
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * Create a Gateway
     *
     * @param  string $requestedName
     * @param  array $options
     * @return $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        if ($this->canCreate($container, $requestedName)) {
            $config = $this->getConfig($container);
            return new $requestedName($config[$requestedName]['env']);
        }
        // $config = $this->getConfig($container);
        // return new $requestedName($config[$requestedName]['env']);
    }

    /**
     * Get db configuration, if any
     *
     * @return array
     */
    protected function getConfig(ContainerInterface $container)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (! $container->has('config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $container->get('config')['module_settings'];
        if (
            ! isset($config['payment'])
            || ! is_array($config['payment'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $config = $config['payment'];
        if (
            ! isset($config['gateways'])
            || ! is_array($config['gateways'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config['gateways'];
        return $this->config;
    }
}
