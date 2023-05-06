<?php

declare(strict_types=1);

namespace Payment;

use Braintree;

return [
    'module_settings' => [
        'payment' => [
            'enabled' => true,
            'active_gateway' => Braintree\Gateway::class,
            'gateways' => [
                Braintree\Gateway::class => [
                    'integration_type' => [
                        'hosted_fields' => false,
                        'drop_in'       => true,
                    ],
                    'runtime_check' => [
                        Service\BraintreeRuntimeCheck::class
                    ],
                    'env' => [
                        'environment' => 'sandbox',
                        'merchantId'  => 'rppzngc4vzpcb2y4',
                        'publicKey'   => 'pqfqhnbfx3f77prh',
                        'privateKey'  => '450426613d7cf2472bcda69bfb5fb7e1',
                    ],
                ],
            ],
        ],
    ],
];