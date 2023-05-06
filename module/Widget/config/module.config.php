<?php

declare(strict_types=1);

namespace Widget;

use App\Controller\Factory\AbstractControllerFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'router'          => [
        'routes' => [
            'widget' => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/widget',
                ],
                'child_routes'  => [
                    'imageslider' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => '/widget/imageslider[/:slideCount]',
                            'constraints' => [
                                'slideCount' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'module_settings' => [
        'widget' => [
            'imageslider' => [
                'enable_imageslider' => '1',
                'paths'              => [
                    'local' => __DIR__ . '/../../public/widget',
                    'web'   => '/widget/imageslider/images',
                ],
            ],
        ],
    ],
    'controllers'     => [
        'aliases'   => [
            'ImageSliderController' => Controller\ImageSliderController::class,
        ],
        'factories' => [
            Controller\ImageSliderController::class => AbstractControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Model\ImageSlider::class => Model\Factory\ImageSliderModelFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];
