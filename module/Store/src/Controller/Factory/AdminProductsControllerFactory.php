<?php

declare(strict_types=1);

namespace Store\Controller\Factory;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Controller\AdminProductsController;
use Store\Db\TableGateway\CategoriesTable;
use Store\Db\TableGateway\ProductsTable;
use Store\Form\ProductForm;
use Store\Model\Product;

class AdminProductsControllerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AdminProductsController
    {
        $formManager = $container->get('FormElementManager');
        return new $requestedName(
            $container->get('config'),
            $container->get(CategoriesTable::class),
            $container->get(ProductsTable::class),
            $container->get(Product::class),
            $formManager->get(ProductForm::class)
        );
    }
}
