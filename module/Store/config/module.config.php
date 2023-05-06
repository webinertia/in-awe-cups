<?php

declare(strict_types=1);

namespace Store;

use App\Controller\Factory\AbstractControllerFactory;
use Laminas\Config\Writer\PhpArray;
use Laminas\Db\Adapter\AdapterServiceDelegator;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory as AbstractFactory;
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
        'products_table_name'                  => 'store_products',
        'store_image_table_name'               => 'store_images',
        'store_categories_table_name'          => 'store_categories',
        'store_options_per_product_table_name' => 'store_options_per_product',
        'store_order_table_name'               => 'store_orders',
    ],
    'session_containers' => [
        'Cart',
        'Search',
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
            Controller\AdminCategoriesController::class     => AbstractControllerFactory::class,
            Controller\AdminController::class               => AbstractControllerFactory::class,
            Controller\AdminProductsController::class       => AbstractControllerFactory::class,
            Controller\ProductsApiController::class         => AbstractControllerFactory::class,
            Controller\CartController::class                => AbstractControllerFactory::class,
            Controller\CategoriesController::class          => AbstractControllerFactory::class,
            Controller\CategoriesApiController::class       => AbstractControllerFactory::class,
            Controller\CategoryImagesController::class      => AbstractControllerFactory::class,
            Controller\CategoryImagesManager::class         => AbstractControllerFactory::class,
            Controller\IndexController::class               => AbstractControllerFactory::class,
            Controller\OrderController::class               => AbstractControllerFactory::class,
            Controller\ProductsController::class            => AbstractControllerFactory::class,
            Controller\ProductSearchController::class       => AbstractControllerFactory::class,
            Controller\ReviewController::class              => AbstractControllerFactory::class,
            Controller\ShippingController::class            => AbstractControllerFactory::class,
            Controller\DataApiController::class             => AbstractControllerFactory::class,
            Controller\ProductOptionsController::class      => AbstractControllerFactory::class,
            Controller\ProductOptionsGroupController::class => AbstractControllerFactory::class,
            Controller\OptionsPerProductController::class   => AbstractControllerFactory::class,
            Controller\ProductImagesController::class       => AbstractControllerFactory::class,
            Controller\ProductImagesManager::class          => AbstractControllerFactory::class,
            Controller\ProductOptionsManager::class         => AbstractControllerFactory::class,
        ],
    ],
    'listeners' => [
        Db\TableGateway\Listener\CategoriesListener::class,
        Db\TableGateway\Listener\ProductsListener::class,
    ],
    'service_manager' => [
        'factories' => [
            Db\TableGateway\Listener\CategoriesListener::class => Db\TableGateway\Listener\Factory\CategoriesListenerFactory::class,
            Db\TableGateway\Listener\ProductsListener::class   => Db\TableGateway\Listener\Factory\ProductsListenerFactory::class,
            Db\TableGateway\ImageTable::class                  => Db\TableGateway\Service\ImageTableFactory::class,
            Db\TableGateway\ProductsTable::class               => Db\TableGateway\Service\ProductsTableFactory::class,
            Db\TableGateway\CategoriesTable::class             => Db\TableGateway\Service\CategoriesTableFactory::class,
            Db\TableGateway\ProductsByCategoryTable::class     => Db\TableGateway\Service\ProductsByCategoryTableFactory::class,
            Db\TableGateway\ProductOptionsTable::class         => Db\TableGateway\Service\ProductOptionsTableFactory::class,
            Db\TableGateway\OrdersTable::class                 => Db\TableGateway\Service\OrdersTableFactory::class,
            Model\Cart::class                                  => Model\Factory\CartFactory::class,
            Model\Category::class                              => Model\Factory\CategoryFactory::class,
            Model\Image::class                                 => Model\Factory\ImageFactory::class,
            Model\OptionsPerProduct::class                     => Model\Factory\OptionsPerProductFactory::class,
            Model\Order::class                                 => Model\Factory\OrderFactory::class,
            Model\OrderData::class                             => Model\Factory\OrderDataFactory::class,
            Model\Product::class                               => Model\Factory\ProductFactory::class,
            Model\ProductByCategory::class                     => Model\Factory\ProductByCategoryFactory::class,
            Model\ProductOptions::class                        => Model\Factory\ProductOptionsFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Api\Form\ApiProductForm::class      => Api\Form\Factory\ApiProductFormFactory::class,
            Api\Form\ApiCategoryForm::class     => Api\Form\Factory\ApiCategoryFactory::class,
            Form\Element\OptionCheckbox::class  => Form\Element\Factory\OptionCheckboxFactory::class,
            Form\DojoTest::class                => InvokableFactory::class,
            Form\CategoryForm::class            => InvokableFactory::class,
            Form\CategoryEditForm::class        => InvokableFactory::class,
            Form\OptionGroupForm::class         => Form\Factory\OptionGroupFormFactory::class,
            Form\ProductForm::class             => InvokableFactory::class,
            Form\ProductOptions::class          => Form\Factory\ProductOptionsFactory::class,
            Form\SearchForm::class              => InvokableFactory::class,
            Form\UploadForm::class              => InvokableFactory::class,
            Form\Fieldset\CategoryInfo::class   => Form\Fieldset\Factory\CategoryInfoFactory::class,
            Form\Fieldset\ProductInfo::class    => Form\Fieldset\Factory\ProductInfoFactory::class,
            Form\Fieldset\SearchFieldset::class => Form\Fieldset\Factory\SearchFactory::class,
            Form\Fieldset\ImageUpload::class    => InvokableFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'categories'    => View\Helper\Categories::class,
            'cart'          => View\Helper\Cart::class,
            'label'         => View\Helper\LabelHelper::class,
            'prepareLabel'  => View\Helper\LabelHelper::class,
            'productRating' => View\Helper\ProductRating::class,
            'productCount'  => View\Helper\ProductCount::class,
        ],
        'factories' => [
            View\Helper\Categories::class    => AbstractFactory::class,
            View\Helper\Cart::class          => View\Helper\Factory\CartFactory::class,
            View\Helper\LabelHelper::class   => View\Helper\Factory\LabelHelperFactory::class,
            View\Helper\ProductRating::class => InvokableFactory::class,
            View\Helper\ProductCount::class  => View\Helper\Factory\ProductCountFactory::class,
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
                ],
            ],
            'product.options.group' => [
                'type' => Segment::class,
                'may_terminate' => true,
                'options' => [
                    'route' => '/admin/store/product/options/group[/:id]',
                    'defaults' => [
                        'controller' => Controller\ProductOptionsGroupController::class,
                    ],
                    'contraints' => [
                        'id'           => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'options.per.product' => [
                'type' => Segment::class,
                'may_terminate' => true,
                'options' => [
                    'route' => '/product[/:id[/:optionGroup]]',
                    'defaults' => [
                        'controller' => Controller\OptionsPerProductController::class,
                    ],
                    'contraints' => [
                        'optionGroup' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]+',
                    ],
                ],
            ],
            'store.product.api' => [
                'type' => Placeholder::class,
                'child_routes' => [
                    'product.list' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'      => '/api/store/product[/:id]',
                            'defaults' => [
                                'controller' => Controller\ProductsApiController::class,
                            ],
                            'constraints' => [
                                'id'       => '[0-9]+',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.store' => [
                'type'          => Placeholder::class,
                'child_routes'  => [
                    'overview'   => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => '/admin/store/overview',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'overview',
                            ],
                        ],
                    ],
                    'settings'   => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => '/admin/store/settings',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'settings',
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
                                    'route'       => '/admin/store/products[/:action[/:productId]]',
                                    'constraints' => [
                                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'productId' => '[0-9]+',
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
                                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id' => '[0-9]+',
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
            'store.category.api' => [
                'type' => Placeholder::class,
                'child_routes' => [
                    'category.data' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/api/store/category[/:id]',
                            'defaults' => [
                                'controller' => Controller\CategoriesApiController::class,
                            ],
                            'constraints' => [
                                'id'       => '[0-9]+',
                            ],
                        ],
                    ],
                ],
            ],
            'product.option.manager' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/admin/store/product/option/manager[/:id]',
                    'defaults' => [
                        'controller' => Controller\ProductOptionsManager::class,
                    ],
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                ],
            ],
            'product.options' => [
                'type' => Segment::class,
                //'may_terminate' => true,
                'options' => [
                    'route' => '/api/admin/store/product/option/provider[/:id]',
                    'defaults' => [
                        'controller' => Controller\ProductOptionsController::class,
                    ],
                    'contraints' => [
                        // 'category'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        // 'optionGroup' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        // 'option'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'          => '[0-9]+',
                    ],
                ],
            ],
            'product.images' => [
                'type' => Segment::class,
                //'may_terminate' => true,
                'options' => [
                    'route' => '/product/images/api[/:id]',
                    'defaults' => [
                        'controller' => Controller\ProductImagesController::class,
                    ],
                    'contraints' => [
                        'id'          => '[0-9]+',
                    ],
                ],
            ],
            'product.images.manager' => [
                'type' => Segment::class,
                //'may_terminate' => true,
                'options' => [
                    'route' => '/product/images/manager[/:id]',
                    'defaults' => [
                        'controller' => Controller\ProductImagesManager::class,
                    ],
                    'contraints' => [
                        'id'          => '[0-9]+',
                    ],
                ],
            ],
            'category.images' => [
                'type' => Segment::class,
                //'may_terminate' => true,
                'options' => [
                    'route' => '/category/images/api[/:id]',
                    'defaults' => [
                        'controller' => Controller\CategoryImagesController::class,
                    ],
                    'contraints' => [
                        'id'          => '[0-9]+',
                    ],
                ],
            ],
            'category.images.manager' => [
                'type' => Segment::class,
                //'may_terminate' => true,
                'options' => [
                    'route' => '/category/images/manager[/:id]',
                    'defaults' => [
                        'controller' => Controller\CategoryImagesManager::class,
                    ],
                    'contraints' => [
                        'id'          => '[0-9]+',
                    ],
                ],
            ],
            'order' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/store/api/order',
                    'defaults' => [
                        'controller' => Controller\OrderController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            // Not currently in use
            'product.search' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/product/search[/:category]',
                    'defaults' => [
                        'controller' => Controller\ProductSearchController::class,
                        'action'     => 'index'
                    ],
                    'contraints' => [
                        'category'          => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
        ],
    ],
    'navigation'         => [
        'default' => [
            [
                'label'     => 'Shop',
                'route'     => 'store/product',
                'resource'  => 'store',
                'privilege' => 'view',
                'class'     => 'nav-link',
                'params'    => ['category' => 'all'],
            ],
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
                        'label'     => '{Create} Categories | Products',
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
                                'refreshOnShow' => false,
                            ],
                            [
                                'dojoType'  => 'ContentPane',
                                'widgetId'  => 'createPageWidget',
                                'label'     => 'Create Product',
                                'uri'       => '/admin/store/products/create',
                                'resource'  => 'store',
                                'privilege' => 'use-store-toolbar',
                                'refreshOnShow' => false,
                            ],
                        ],
                    ],
                    [
                        'dojoType'  => 'TabContainer',
                        'widgetId'  => 'storeDataEditor',
                        'label'     => '{Manage} Categories | Products | Options',
                        'uri'       => '/admin/store/manager',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                        'pages' => [
                            [
                                'dojoType'      => 'ContentPane',
                                'widgetId'      => 'editCategoryWidget',
                                'label'         => 'Manage Categories',
                                'uri'           => '/admin/store/manage/categories',
                                'resource'      => 'store',
                                'privilege'     => 'use-store-toolbar',
                                'refreshOnShow' => true,
                            ],
                            [
                                'dojoType'      => 'ContentPane',
                                'widgetId'      => 'editProductWidget',
                                'label'         => 'Manage Products',
                                'uri'           => '/admin/store/products',
                                'resource'      => 'store',
                                'privilege'     => 'use-store-toolbar',
                                'refreshOnShow' => true,
                            ],
                            [
                                'dojoType'      => 'ContentPane',
                                'widgetId'      => 'editOptionManagerWidget',
                                'label'         => 'Manage Options',
                                'uri'           => '/api/admin/store/product/option/manager',
                                'resource'      => 'store',
                                'privilege'     => 'use-store-toolbar',
                                'refreshOnShow' => true,
                            ],
                        ],
                    ],
                    [
                        'dojoType'  => 'ContentPane',
                        'widgetId'  => 'storeSettingsEditor',
                        'label'     => 'Manage Settings',
                        'uri'       => '/admin/store/settings',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
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
    'view_manager' => [
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];
