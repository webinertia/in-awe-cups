<?php

declare(strict_types=1);

namespace App;

use App\Log\Processors\PsrPlaceholder;
use ContentManager\Controller\ContentController;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\Log\Logger;
use Laminas\Mvc\I18n\Router\TranslatorAwareTreeRouteStack;
use Laminas\Mvc\Service\ViewTemplatePathStackFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\SaveHandler\SaveHandlerInterface;
use Psr\Log\LoggerInterface;

use function rand;

return [
    'app_settings'       => [ // app_settings that are not to be edited are stored here
        'server' => [
            'app_path'        => __DIR__ . '/../../../',
            'upload_basepath' => __DIR__ . '/../../../public/modules',
            'captcha_path'    => __DIR__ . '/../../../public/modules/app/captcha',
            'scheme'          => $_SERVER['REQUEST_SCHEME'] ?? 'http',
            'content_security_policy' => [
                'directives' => [
                    'default-src' => ['\'self\'', 'unsafe-inline'],
                    'img-src'     => ['*'],
                    'script-src'  => ['\'self\'', 'unsafe-inline'],
                ],
            ],
        ],
        'theme' => [
            'admin_template' => 'layout/admin',
        ],
    ],
    'base_dir'           => __DIR__ . '/../../../',
    'db'                 => [
        'sessions_table_name' => 'sessions',
        'log_table_name'      => 'log',
        'theme_table_name'    => 'theme',
    ],
    'router'             => [
        'router_class' => TranslatorAwareTreeRouteStack::class,
        'routes'       => [
            'page'    => [
                'type'          => Segment::class,
                'may_terminate' => true,
                'options'       => [
                    'route'       => '[/:title]',
                    'constraints' => [
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults'    => [
                        'controller' => ContentController::class,
                        'action'     => 'page',
                    ],
                ],
            ],
            'home'    => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'test'    => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/test',
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'site'    => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/site[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'contact' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/site/contact[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'contact',
                    ],
                ],
            ],
            'admin'   => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'child_routes'  => [
                    'dashboard' => [
                        'type'          => Literal::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'    => '/admin',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'settings'  => [
                        'type'          => Placeholder::class,
                        'may_terminate' => true,
                        'child_routes'  => [
                            'manage' => [
                                'may_terminate' => true,
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/admin/settings',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'manage-settings',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'themes'    => [
                        'type'          => Placeholder::class,
                        'may_terminate' => true,
                        'child_routes'  => [
                            'manage' => [
                                'may_terminate' => true,
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/admin/themes',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'manage-themes',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'logs'      => [
                        'type'          => Placeholder::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route' => '/admin/logs',
                        ],
                        'child_routes'  => [
                            'overview' => [
                                'may_terminate' => true,
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'    => '/admin/logs/view',
                                    'defaults' => [
                                        'controller' => Controller\LogController::class,
                                        'action'     => 'view',
                                    ],
                                ],
                            ],
                            'delete'   => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => '/admin/logs/delete[/:id]',
                                    'defaults'    => [
                                        'controller' => Controller\LogController::class,
                                        'action'     => 'delete',
                                    ],
                                    'constraints' => [
                                        'id' => '[0-9]*',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'psr_log'            => [
        LoggerInterface::class => [
            'writers'    => [
                'db' => [
                    'name'     => 'db',
                    'priority' => Logger::INFO,
                    'options'  => [
                        'table'     => 'log',
                        'db'        => AdapterInterface::class,
                        'formatter' => [
                            'name'    => 'db',
                            'options' => [
                                'dateTimeFormat' => 'm-d-Y H:i:s',
                            ],
                        ],
                    ],
                ],
            ],
            'processors' => [
                'psrplaceholder' => [
                    'name'     => PsrPlaceholder::class,
                    'priority' => Logger::INFO,
                ],
            ],
        ],
    ],
    'log_processors'     => [
        'aliases'   => [
            'psrplaceholder' => PsrPlaceholder::class,
        ],
        'factories' => [
            PsrPlaceholder::class => Log\Processors\PsrPlaceholderFactory::class,
        ],
    ],
    'listeners'          => [
        Log\LogListener::class,
        Listener\AdminListener::class,
        Upload\UploadListener::class,
    ],
    'service_manager'    => [
        'aliases' => [
            'ViewTemplatePathStack' => View\Resolver\TemplatePathStack::class,
        ],
        'factories' => [
            ConfigInterface::class                 => Session\ConfigFactory::class,
            Session\Container::class               => Session\ContainerFactory::class,
            Db\DbGateway\LogGateway::class         => Db\DbGateway\Factory\LogGatewayFactory::class,
            Log\LogListener::class                 => Log\LogListenerFactory::class,
            Listener\AdminListener::class          => Listener\Factory\AdminListenerFactory::class,
            Model\Settings::class                  => Model\Factory\SettingsFactory::class,
            Model\Theme::class                     => InvokableFactory::class,
            Service\Email::class                   => Service\Factory\EmailFactory::class,
            SaveHandlerInterface::class            => Session\SaveHandlerFactory::class,
            Upload\UploadListener::class           => Upload\UploadListenerFactory::class,
            View\Resolver\TemplatePathStack::class => ViewTemplatePathStackFactory::class
        ],
        'delegators' => [
            View\Resolver\TemplatePathStack::class => [
                View\Resolver\TemplatePathStackFactory::class
            ],
        ],
    ],
    'controllers'        => [
        'factories' => [ // move this to an abstract factory???
            Controller\AdminController::class => Controller\Factory\AbstractControllerFactory::class,
            Controller\IndexController::class => Controller\Factory\AbstractControllerFactory::class,
            Controller\TestController::class  => Controller\Factory\AbstractControllerFactory::class,
            Controller\LogController::class   => Controller\Factory\AbstractControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'email'      => Controller\Plugin\Email::class,
            'getService' => Controller\Plugin\ServiceLocator::class,
        ],
        'factories' => [
            Controller\Plugin\Email::class          => Controller\Plugin\Factory\EmailFactory::class,
            Controller\Plugin\ServiceLocator::class => Controller\Plugin\Factory\ServiceLocatorFactory::class,
        ],
    ],
    'form_elements'      => [
        'factories' => [
            Form\Fieldset\AppSettingsFieldset::class => Form\Fieldset\Factory\AppSettingsFieldsetFactory::class,
            Form\ContactForm::class                  => Form\Factory\ContactFormFactory::class,
            Form\Fieldset\SecurityFieldset::class    => Form\Fieldset\Factory\SecurityFieldsetFactory::class,
            Form\SettingsForm::class                 => Form\Factory\SettingsFormFactory::class,
            Form\ThemeSettingsForm::class            => Form\Factory\ThemeSettingsFormFactory::class,
            Form\Fieldset\ThemeFieldset::class       => InvokableFactory::class,
        ],
    ],
    'filters'            => [
        'factories' => [
            Filter\DbDateFormatter::class      => InvokableFactory::class,
            Filter\FqcnToControllerName::class => InvokableFactory::class,
            Filter\FqcnToModuleName::class     => InvokableFactory::class,
            Filter\TitleToLabel::class         => InvokableFactory::class,
            Filter\LabelToTitle::class         => InvokableFactory::class,
            Filter\PadFloatString::class       => InvokableFactory::class,
        ],
    ],
    'navigation'         => [
        'default' => [
            [
                'label'  => 'Home',
                'route'  => 'home',
                'class'  => 'nav-link',
                'order'  => -999,
                'action' => 'index',
            ],
            [
                'label'  => 'Contact Us',
                'route'  => 'contact',
                'class'  => 'nav-link',
                'order'  => 999,
                'action' => 'contact',
            ],
            [
                'label'     => 'Admin',
                'uri'       => '/admin',
                'class'     => 'nav-link',
                'order'     => -1000,
                'resource'  => 'admin',
                'privilege' => 'view',
            ],
        ],
        'admin'   => [
            [
                'dojoType'  => 'Button',
                'widgetId'  => 'homeButton',
                'label'     => 'Home',
                'uri'       => '/',
                'iconClass' => 'mdi mdi-home text-success',
                'order'     => -1000,
            ],
            // [
            //     'label'     => 'Dashboard',
            //     'uri'       => '/admin',
            //     'iconClass' => 'mdi mdi-speedometer text-success',
            //     'order'     => -99,
            // ],
            // [
            //     'dojoType'  => 'ContentPane',
            //     'widgetId'  => 'settingManager',
            //     'label'     => 'Manage Settings',
            //     'uri'       => '/admin/settings',
            //     'iconClass' => 'mdi mdi-cogs text-danger',
            //     'resource'  => 'settings',
            //     'privilege' => 'edit',
            // ],
            [
                'dojoType'  => 'ContentPane',
                'widgetId'  => 'themeManager',
                'label'     => 'Manage Themes',
                'uri'       => '/admin/themes',
                'iconClass' => 'mdi mdi-palette text-success',
                'resource'  => 'theme',
                'privilege' => 'manage',
            ],
            [
                'dojoType'  => 'ContentPane',
                'widgetId'  => 'logManager',
                'label'     => 'Logs',
                'uri'       => '/admin/logs/view',
                'iconClass' => 'mdi mdi-alarm text-warning',
                'resource'  => 'logs',
                'privilege' => 'view',
                'order'     => 1000,
            ],
        ],
    ],
    'view_helpers'       => [
        'aliases'   => [
            'bootstrapForm'           => View\Helper\BootstrapForm::class,
            'bootstrapFormCollection' => View\Helper\BootstrapFormCollection::class,
            'bootstrapFormRow'        => View\Helper\BootstrapFormRow::class,
            'config'                  => View\Helper\Config::class,
            'mapPriority'             => View\Helper\MapLogPriority::class,
            'jsonifyMenu'             => View\Helper\JsonMenu::class,
        ],
        'factories' => [
            View\Helper\MapLogPriority::class          => InvokableFactory::class,
            View\Helper\BootstrapForm::class           => InvokableFactory::class,
            View\Helper\BootstrapFormCollection::class => InvokableFactory::class,
            View\Helper\BootstrapFormRow::class        => InvokableFactory::class,
            View\Helper\Config::class                  => View\Helper\Factory\ConfigFactory::class,
            View\Helper\JsonMenu::class                => InvokableFactory::class,
        ],
    ],
    'view_manager'       => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            // 'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            // // 'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            // 'error/404'               => __DIR__ . '/../view/error/404.phtml',
            // 'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'strategies'               => [
            'ViewJsonStrategy',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
    'translator'         => [
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
                'filename'    => __DIR__ . '/../language/es_MX.php',
                'locale'      => 'es_MX',
                'text_domain' => 'default',
            ],
            [
                'type'        => 'PhpArray',
                'filename'    => __DIR__ . '/../language/log_messages_en_US.php',
                'locale'      => 'es_US',
                'text_domain' => 'default',
            ],
            [
                'type'        => 'PhpArray',
                'filename'    => __DIR__ . '/../language/log_messages_es_MX.php',
                'locale'      => 'es_MX',
                'text_domain' => 'default',
            ],
        ],
        'event_manager_enabled'     => true,
    ],
];
