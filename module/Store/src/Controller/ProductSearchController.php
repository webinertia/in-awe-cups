<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Store\Model\Product;

final class ProductSearchController extends AbstractAppController
{
    /** @var Product $products */
    protected $products;

    public function __construct(Product $product)
    {
        $this->products = $product;
    }

    public function indexAction(): ModelInterface
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }

        return $this->view;
    }
}
