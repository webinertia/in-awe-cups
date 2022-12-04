<?php

declare(strict_types=1);

namespace Store;

use App\Controller\Factory\AbstractControllerFactory;
use Laminas\Config\Writer\PhpArray;
use Laminas\Db\Adapter\AdapterServiceDelegator;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\I18n\Translator\Loader\PhpArray as I18nPhpArray;

return [
    'app_settings'       => [
        'load_store_as_homepage' => '1',
    ],
    'module_settings'    => [
        'store' => [
            'upload' => [
                'renameUploadConfig' => [
                    'randomize'            => true,
                    'use_upload_extension' => true,
                ],
            ],
        ],
    ],
    'db'                 => [
        'products_table_name'         => 'store_products',
        'store_image_table_name'      => 'store_images',
        'store_categories_table_name' => 'store_categories',
    ],
    'session_containers' => [
        'Cart_Context',
    ],
    'controllers'        => [
        'aliases'   => [
            'CategoriesController' => Controller\CategoriesController::class,
            'StoreController'      => Controller\IndexController::class,
            'ProductsController'   => Controller\ProductsController::class,
            'ReviewController'     => Controller\ReviewController::class,
            'StoreDataApi'         => Controller\DataApiController::class,
        ],
        'factories' => [
            Controller\AdminCategoriesController::class => AbstractControllerFactory::class,
            Controller\AdminController::class           => AbstractControllerFactory::class,
            Controller\AdminProductsController::class   => AbstractControllerFactory::class,
            Controller\ApiController::class             => AbstractControllerFactory::class,
            Controller\CartController::class            => AbstractControllerFactory::class,
            Controller\CategoriesController::class      => AbstractControllerFactory::class,
            Controller\IndexController::class           => AbstractControllerFactory::class,
            Controller\OrderController::class           => AbstractControllerFactory::class,
            Controller\ProductsController::class        => AbstractControllerFactory::class,
            Controller\ProductSearchController::class   => AbstractControllerFactory::class,
            Controller\ReviewController::class          => AbstractControllerFactory::class,
            Controller\ShippingController::class        => AbstractControllerFactory::class,
            Controller\DataApiController::class         => AbstractControllerFactory::class,
        ],
    ],
    'listeners' => [
        Db\TableGateway\Listener\CategoriesListener::class,
    ],
    'service_manager' => [
        'factories' => [
            Db\TableGateway\Listener\CategoriesListener::class => InvokableFactory::class,
            Db\TableGateway\ImageTable::class                  => Db\TableGateway\Service\ImageTableFactory::class,
            Db\TableGateway\ProductsTable::class               => Db\TableGateway\Service\ProductsTableFactory::class,
            Db\TableGateway\CategoriesTable::class             => Db\TableGateway\Service\CategoriesTableFactory::class,
            Db\TableGateway\ProductsByCategoryTable::class     => Db\TableGateway\Service\ProductsByCategoryTableFactory::class,
            Db\TableGateway\OrdersTable::class                 => Db\TableGateway\Service\OrdersTableFactory::class,
            Model\Cart::class                                  => Model\Factory\CartFactory::class,
            Model\Category::class                              => Model\Factory\CategoryFactory::class,
            Model\Image::class                                 => Model\Factory\ImageFactory::class,
            Model\Product::class                               => Model\Factory\ProductFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\DojoTest::class                => InvokableFactory::class,
            Form\CategoryForm::class            => InvokableFactory::class,
            Form\ProductForm::class             => InvokableFactory::class,
            Form\SearchForm::class              => InvokableFactory::class,
            Form\Fieldset\CategoryInfo::class   => Form\Fieldset\Factory\CategoryInfoFactory::class,
            Form\Fieldset\ProductInfo::class    => Form\Fieldset\Factory\ProductInfoFactory::class,
            Form\Fieldset\SearchFieldset::class => Form\Fieldset\Factory\SearchFactory::class,
            Form\Fieldset\ImageUpload::class    => InvokableFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'productRating' => View\Helper\ProductRating::class,
            'label'         => View\Helper\LabelHelper::class,
            'prepareLabel'  => View\Helper\LabelHelper::class,
        ],
        'factories' => [
            View\Helper\LabelHelper::class   => View\Helper\Factory\LabelHelperFactory::class,
            View\Helper\ProductRating::class => InvokableFactory::class,
        ],
    ],
    'router'             => [
        'routes' => [
            'store'       => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route'    => '/store',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'child_routes' => [
                    'product' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/store[/:category[/:product]]',
                            'defaults' => [
                                'controller' => Controller\ProductsController::class,
                                'action'     => 'index',
                            ],
                            'constraints' => [
                                'category' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'product'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                    'category' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/store[/:category]',
                            'defaults' => [
                                'controller' => Controller\CategoriesController::class,
                                'action' => 'index',
                            ],
                            'constraints' => [
                                'category' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                    'cart' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/store/cart[/:action]',
                            'defaults' => [
                                'controller' => Controller\CartController::class,
                                'action' => 'index',
                            ],
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                    'order' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/store/order[/:action[/:orderId]]',
                            'defaults' => [
                                'controller' => Controller\OrderController::class,
                                'action'     => 'index',
                            ],
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'orderId' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.store' => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                // 'options'       => [
                //     'route' => '/admin/store',
                // ],
                'child_routes'  => [
                    'overview'   => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'    => '/admin/store[/:action]',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'overview',
                            ],
                        ],
                    ],
                    'manage' => [
                        'type' => Placeholder::class,
                        'may_terminate' => true,
                        'child_routes' => [
                            'products'   => [
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'       => '/admin/store/manage/products[/:action[/:id]]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AdminProductsController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                            ],
                            'categories' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/admin/store/manage/categories[/:action[/:id]]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AdminCategoriesController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'store.api' => [
                'type' => Placeholder::class,
                'may_terminate' => true,
                'options' => [
                    'route' => '/store/api',
                ],
                'child_routes' => [
                    'category.data' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'      => '/store/data/api/category[/:id[/:listType]]',
                            'defaults' => [
                                'controller' => Controller\DataApiController::class,
                                'action'     => 'category-data',
                            ],
                            'constraints' => [
                                'id'       => '[0-9]+',
                                'listType' => '[a-zA-Z]*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'navigation'         => [
        'default' => [
            // [
            //     'label'     => 'Store',
            //     'route'     => 'store',
            //     'class'     => 'nav-link',
            //     //'resource'  => 'store', // todo: add store resource and privileges
            //     //'privilege' => 'view',
            //     'action'    => 'index',
            // ],
            [
                'label'     => 'Shop',
                'route'     => 'store/category',
                'resource'  => 'store',
                'privilege' => 'view',
                'class'     => 'nav-link',
                'params'    => ['category' => 'all'],
            ],
            // [
            //     'label'     => 'Products',
            //     'route'     => 'store/products',
            //     'resource'  => 'store',
            //     'privilege' => 'view',
            // ],
        ],
        'admin'   => [
            [
                'menuId'    => 'store',
                'dojoType'  => 'TabContainer',
                'widgetId'  => 'storeManager',
                'label'     => 'Manage Store',
                'uri'       => '/admin/store/overview',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
                'name'      => 'Manage Store',
                'order'     => -10,
                'iconClass' => 'mdi mdi-laptop',
                'class' => 'show',
                'pages'     => [
                    [
                        'dojoType'  => 'ContentPane',
                        'widgetId'  => 'storeOverview',
                        'label'     => 'Store Overview',
                        'uri'       => '/admin/store/overview',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                    ],
                    [
                        'dojoType'  => 'TabContainer',
                        'widgetId'  => 'storeDataManager',
                        'label'     => 'Manage Store Data',
                        'uri'       => '/admin/store/manager',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                        'pages' => [
                            [
                                'dojoType'  => 'ContentPane',
                                'widgetId'  => 'createCategoryWidget',
                                'label'     => 'Create Category',
                                'uri'       => '/admin/store/manage/categories/create',
                                'resource'  => 'store',
                                'privilege' => 'use-store-toolbar',
                            ],
                            [
                                'dojoType'  => 'ContentPane',
                                'widgetId'  => 'createPageWidget',
                                'label'     => 'Create Product',
                                'uri'       => '/admin/store/manage/products/create',
                                'resource'  => 'store',
                                'privilege' => 'use-store-toolbar',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator'         => [
        'translation_file_patterns' => [ // This is the only config that is needed for 1 translation per file
            [
                'type' => I18nPhpArray::class,
                'filename' => 'en_US.php',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
        ],
    ],
];
