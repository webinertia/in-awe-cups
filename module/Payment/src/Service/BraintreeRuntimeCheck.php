<?php

declare(strict_types=1);

namespace Payment\Service;

use Braintree;
use function extension_loaded;

final class BraintreeRuntimeCheck
{
    protected array $requiredExtensions = ['xmlwriter', 'openssl', 'dom', 'hash', 'curl'];
    public function __invoke()
    {
        foreach ($this->requiredExtensions as $ext) {
            if (! extension_loaded($ext)) {
                throw new Braintree\Exception('The Braintree library requires the ' . $ext . ' extension.');
            }
        }
        return true;
    }
}
