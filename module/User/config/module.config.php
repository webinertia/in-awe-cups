<?php

declare(strict_types=1);

namespace User;

use App\Controller\Factory\AbstractControllerFactory;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\View\Helper\Navigation;
use User\Navigation\View\PermissionAclDelegatorFactory;
use User\Navigation\View\RoleFromAuthenticationIdentityDelegator;

return [
    'module_settings'    => [
        'user' => [
            'server' => [
                'profile_image_target_path' => '/user/profile/profileImages/profileImage',
            ],
        ],
    ],
    'db'                 => [
        'auth_identity_column'   => 'userName',
        'auth_credential_column' => 'password',
        'users_table_name'       => 'users',
    ],
    'laminas-cli'        => [
        'commands' => [
            'create-user' => Command\CreateUserCommand::class,
        ],
    ],
    'router'             => [
        'routes' => [
            'user'       => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/user',
                ],
                'child_routes'  => [
                    'list'           => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'       => '/user/list[/:page[/:count]]',
                            'constraints' => [
                                'page'  => '[0-9]*',
                                'count' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\UserController::class,
                                'action'     => 'list',
                                'page'       => 1,
                                'count'      => 10,
                            ],
                        ],
                    ],
                    'register'       => [
                        'type'          => Literal::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'    => '/user/register',
                            'defaults' => [
                                'controller' => Controller\RegisterController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'verify'         => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/user/register/verify',
                            'defaults' => [
                                'controller' => Controller\RegisterController::class,
                                'action'     => 'verify',
                            ],
                        ],
                    ],
                    'profile'        => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'       => '/user/profile[/:action[/:userName]]',
                            'constraints' => [
                                'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ProfileController::class,
                                'action'     => 'view',
                            ],
                        ],
                    ],
                    'manage-profile' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => '/user/manage-profile[/:action[/:userName[/:section]]]',
                            'constraints' => [
                                'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'section'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ManageProfileController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'account'        => [
                        'type'          => Placeholder::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route' => '/user/account',
                        ],
                        'child_routes'  => [
                            'dashboard'        => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/account/dashboard[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'dashboard',
                                    ],
                                ],
                            ],
                            'login'            => [
                                'type'          => Literal::class,
                                'may_terminate' => false,
                                'options'       => [
                                    'route'    => '/user/account/login',
                                    'defaults' => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'login',
                                    ],
                                ],
                            ],
                            'edit'             => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/account/edit[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'edit',
                                    ],
                                ],
                            ],
                            'delete'           => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/account/delete[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'delete',
                                    ],
                                ],
                            ],
                            'logout'           => [
                                'type'          => Literal::class,
                                'may_terminate' => false,
                                'options'       => [
                                    'route'    => '/user/account/logout',
                                    'defaults' => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'logout',
                                    ],
                                ],
                            ],
                            'password'         => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/acount/password[/:action[/:step]]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'step'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\PasswordController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                            ],
                            'staff-activate'   => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/account/staff-activate[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'staffActivate',
                                    ],
                                ],
                            ],
                            'staff-deactivate' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/account/staff-deactivate[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'staffDeactivate',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'admin.user' => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/admin/user',
                ],
                'child_routes'  => [
                    'overview' => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'       => '/admin/user[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'widgets'    => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/user/widgets[/:action[/:group[/:page[/:itemsPerPage]]]]',
                    'constraints' => [
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'group'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'         => '[0-9]+',
                        'itemsPerPage' => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => Controller\WidgetController::class,
                        'action'     => 'list',
                    ],
                ],
            ],
        ],
    ],
    'navigation'         => [
        'default' => [
            [
                'label'     => 'Users',
                'route'     => 'user/list',
                'class'     => 'nav-link',
                'order'     => -901,
                'action'    => 'list',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
            ],
            [
                'label'     => 'Profile',
                'route'     => 'user/profile',
                'class'     => 'nav-link',
                'order'     => -900,
                'action'    => 'view',
                'resource'  => 'users',
                'privilege' => 'view',
            ],
            [
                'label'     => 'Login',
                'route'     => 'user/account/login',
                'class'     => 'nav-link',
                'action'    => 'login',
                'resource'  => 'account',
                'privilege' => 'login',
                'order'     => 1000,
            ],
            [
                'label'     => 'Logout',
                'route'     => 'user/account/logout',
                'class'     => 'nav-link',
                'action'    => 'logout',
                'resource'  => 'account',
                'privilege' => 'logout',
                'order'     => 1001,
            ],
            [
                'label'     => 'Register',
                'route'     => 'user/register',
                'class'     => 'nav-link',
                'action'    => 'index',
                'resource'  => 'account',
                'privilege' => 'register',
                'order'     => 1000,
            ],
        ],
        'admin'   => [
            [
                'dojoType'  => 'ContentPane',
                'widgetId'  => 'userManager',
                'label'     => 'Manage Users',
                'uri'       => '/admin/user/index',
                'iconClass' => 'mdi mdi-account-multiple text-primary',
                'action'    => 'index',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
            ],
            [
                'dojoType'  => 'Button',
                'widgetId'  => 'logoutButton',
                'label'     => 'Logout',
                'uri'       => '/user/account/logout',
                'iconClass' => 'mdi mdi-logout text-success',
                'action'    => 'logout',
                'resource'  => 'user',
                'privilege' => 'logout',
                'order'     => 1001,
            ],
        ],
    ],
    'navigation_helpers' => [
        'delegators' => [
            Navigation::class => [
                PermissionAclDelegatorFactory::class,
                RoleFromAuthenticationIdentityDelegator::class,
            ],
        ],
    ],
    'controllers'        => [
        'factories' => [
            Controller\AccountController::class       => AbstractControllerFactory::class,
            Controller\AdminController::class         => AbstractControllerFactory::class,
            Controller\ManageProfileController::class => AbstractControllerFactory::class,
            Controller\PasswordController::class      => AbstractControllerFactory::class,
            Controller\ProfileController::class       => AbstractControllerFactory::class,
            Controller\RegisterController::class      => AbstractControllerFactory::class,
            Controller\UserController::class          => AbstractControllerFactory::class,
            Controller\WidgetController::class        => AbstractControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'identity' => Controller\Plugin\Identity::class,
            'acl'      => Controller\Plugin\Acl::class,
        ],
        'factories' => [
            Controller\Plugin\Identity::class => Controller\Plugin\Factory\IdentityFactory::class,
            Controller\Plugin\Acl::class      => Controller\Plugin\Factory\AclFactory::class,
        ],
    ],
    'service_manager'    => [
        'aliases'   => [
            'UserInterface'                     => Service\UserServiceInterface::class,
            Service\UserServiceInterface::class => Service\UserService::class,
        ],
        'factories' => [
            AclInterface::class                    => Acl\AclFactory::class,
            AuthenticationService::class           => Authentication\AuthenticationServiceFactory::class,
            Command\CreateUserCommand::class       => Command\Factory\CreateUserCommandFactory::class,
            Db\Listener\UserGatewayListener::class => Db\Listener\UserGatewayListenerFactory::class,
            Db\UserGateway::class                  => Db\Factory\UserGatewayFactory::class,
            Model\Roles::class                     => InvokableFactory::class,
            Model\Guest::class                     => InvokableFactory::class,
            Service\UserService::class             => Service\Factory\UserServiceFactory::class,
        ],
    ],
    'filters'            => [
        'factories' => [
            Filter\PasswordFilter::class   => InvokableFactory::class,
            Filter\RegistrationHash::class => InvokableFactory::class,
        ],
    ],
    'form_elements'      => [
        'factories' => [
            Form\Element\RoleSelect::class           => Form\Element\Factory\RoleSelectFactory::class,
            Form\Fieldset\AcctDataFieldset::class    => Form\Fieldset\Factory\AcctDataFieldsetFactory::class,
            Form\Fieldset\LoginFieldset::class       => Form\Fieldset\Factory\LoginFieldsetFactory::class,
            Form\Fieldset\PasswordFieldset::class    => Form\Fieldset\Factory\PasswordFieldsetFactory::class,
            Form\Fieldset\ProfileFieldset::class     => Form\Fieldset\Factory\ProfileFieldsetFactory::class,
            Form\Fieldset\RoleFieldset::class        => Form\Fieldset\Factory\RoleFieldsetFactory::class,
            Form\Fieldset\SocialMediaFieldset::class => Form\Fieldset\Factory\SocialMediaFieldsetFactory::class,
            Form\UserForm::class                     => Form\Factory\UserFormFactory::class,
            Form\ProfileForm::class                  => Form\Factory\ProfileFormFactory::class,
        ],
    ],
    'view_helpers'       => [
        'aliases'   => [
            'acl'             => View\Helper\Acl::class,
            'aclawarecontrol' => View\Helper\AclAwareControl::class,
            'aclAwareControl' => View\Helper\AclAwareControl::class,
            'aclControl'      => View\Helper\AclAwareControl::class,
            'identity'        => View\Helper\Identity::class,
        ],
        'factories' => [
            View\Helper\Acl::class             => View\Helper\Factory\AclFactory::class,
            View\Helper\AclAwareControl::class => View\Helper\Factory\AclAwareControlFactory::class,
            View\Helper\Identity::class        => View\Helper\Factory\IdentityFactory::class,
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
                'filename'    => __DIR__ . '/../language/en_MX.php',
                'locale'      => 'en_MX',
                'text_domain' => 'default',
            ],
            [
                'type'        => 'PhpArray',
                'filename'    => __DIR__ . '/../language/log_messages_en_US.php',
                'locale'      => 'en_US',
                'text_domain' => 'default',
            ],
            [
                'type'        => 'PhpArray',
                'filename'    => __DIR__ . '/../language/log_messages_es_MX.php',
                'locale'      => 'en_MX',
                'text_domain' => 'default',
            ],
        ],
    ],
    'widgets'            => [
        'member_list'       => [
            'items_per_page' => 2,
            'display_groups' => 'all',
            'widget_name'    => 'Member List',
        ],
        'admin_member_list' => [
            'items_per_page' => 5,
            'display_groups' => 'admin',
            'widget_name'    => 'Administrators',
        ],
    ],
];
