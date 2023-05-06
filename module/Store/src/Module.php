<?php

declare(strict_types=1);

namespace Store;

use Laminas\Config\Factory;

class Module
{
    public function getConfig(): array
    {
        return Factory::fromFiles([__DIR__ . '/../config/module.config.php', __DIR__ . '/../config/module.settings.php']);
    }
}
