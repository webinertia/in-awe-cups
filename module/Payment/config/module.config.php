<?php

declare(strict_types=1);

namespace Payment;

use App\Controller\Factory\AbstractControllerFactory;
use Braintree;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory as AbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\GatewayController::class => AbstractControllerFactory::class,
        ],
    ],
    'listeners' => [],
    'service_manager' => [
        'factories' => [
            Braintree\Gateway::class => Gateway\AbstractServiceFactory::class,
            Service\Gateway::class   => Service\GatewayServiceFactory::class,
        ],
    ],
    'session_containers' => [
        Service\Gateway::SESSION_CONTAINER_NAME,
    ],
    'router' => [
        'routes' => [
            'payment' => [
                'type' => Placeholder::class,
                'may_terminate' => true,
                'options' => [
                    'route' => 'payment',
                ],
                'child_routes' => [
                    'gateway' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/payment[/:gateway[/:action]]',
                            'defaults' => [
                                'controller' => Controller\GatewayController::class, //restful controller
                                'action'     => 'index',
                                'gateway' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];