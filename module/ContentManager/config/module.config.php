<?php

declare(strict_types=1);

namespace ContentManager;

use App\Controller\Factory\AbstractControllerFactory;
use ContentManager\Navigation\Service\DefaultNavigationFactory;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\Navigation\Navigation;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'db'                => [
        'pages_table_name' => 'pages',
    ],
    'page_upload_paths' => [
        'local_path'  => '/public/modules/contentmanager/page/content/images/',
        'public_path' => '/modules/contentmanager/page/content/images/',
    ],
    'router'            => [
        'routes' => [
            'admin.content' => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/admin/content',
                ],
                'child_routes'  => [
                    'manager' => [
                        'type'          => Literal::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'    => '/admin/content/manager',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'dashboard',
                            ],
                        ],
                        'child_routes'  => [
                            'create' => [
                                'type'          => Literal::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'    => '/create',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'create',
                                    ],
                                ],
                            ],
                            'edit'   => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'    => '/edit[/:title]',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'edit',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/delete[/:id]',
                                    'defaults'    => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'delete',
                                    ],
                                    'constraints' => [
                                        'id' => '[0-9]*',
                                    ],
                                ],
                            ],
                            'upload' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'    => '/upload',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'upload-images',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'       => [
        'factories' => [
            Controller\AdminController::class   => AbstractControllerFactory::class,
            Controller\ContentController::class => AbstractControllerFactory::class,
        ],
    ],
    'form_elements'     => [
        'factories' => [
            Form\PageForm::class              => Form\Factory\PageFormFactory::class,
            Form\Fieldset\PageFieldset::class => Form\Fieldset\Factory\PageFieldsetFactory::class,
        ],
    ],
    'service_manager'   => [
        'aliases'   => [
            'navigation' => Navigation::class,
        ],
        'factories' => [
            Navigation::class                      => DefaultNavigationFactory::class,
            Db\PageGateway::class                  => Db\Factory\PageGatewayFactory::class,
            Db\Listener\PageGatewayListener::class => Db\Listener\PageGatewayListenerFactory::class,
            Model\Page::class                      => Model\Factory\PageFactory::class,
        ],
    ],
    'translator'        => [
        'translation_file_patterns' => [
            [
                'type'     => PhpArray::class,
                'filename' => 'en_US.php',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
        ],
        'translation_files'         => [
            [
                'type'        => 'PhpArray',
                'filename'    => __DIR__ . '/../language/en_US.php',
                'locale'      => 'en_US',
                'text_domain' => 'default',
            ],
            [
                'type'        => 'PhpArray',
                'filename'    => __DIR__ . '/../language/log_messages_en_US.php',
                'locale'      => 'en_US',
                'text_domain' => 'default',
            ],
        ],
    ],
    'navigation'        => [
        'admin' => [
            [
                'dojoType'  => 'TabContainer',
                'widgetId'  => 'contentManager',
                'label'     => 'Content Manager',
                'uri'       => '/admin/content',
                'iconClass' => 'mdi mdi-page-layout-body text-warning',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
                'pages'     => [
                    [
                        'dojoType'  => 'ContentPane',
                        'widgetId'  => 'contentOverview',
                        'label'     => 'Content Dashboard',
                        'uri'     => 'admin/content/manager',
                        'action'    => 'dashboard',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                    ],
                    [
                        'dojoType'  => 'ContentPane',
                        'widgetId'  => 'pageCreator',
                        'label'     => 'Create New Page',
                        'uri'       => 'admin/content/manager/create',
                        'action'    => 'create',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
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
