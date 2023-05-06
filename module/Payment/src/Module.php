<?php

declare(strict_types=1);

namespace Payment;

use Braintree;
use Laminas\Config\Factory;

use function extension_loaded;

class Module
{
    public function getConfig(): array
    {
        return Factory::fromFiles([__DIR__ . '/../config/module.config.php', __DIR__ . '/../config/module.settings.php']);
    }

    public function onBootstrap($event)
    {
        $requiredExtensions = ['xmlwriter', 'openssl', 'dom', 'hash', 'curl'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                throw new Braintree\Exception('The Braintree library requires the ' . $ext . ' extension.');
            }
        }
    }
}
