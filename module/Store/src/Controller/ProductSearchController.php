<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Store\Model\Image;
use Store\Model\Product;
use Store\Model\OptionsPerProduct;

final class ProductSearchController extends AbstractAppController
{
    /** @var Image $image */
    protected $image;
    /** @var Product $products */
    protected $products;
    /** @var OptionsPerProduct $optionLookup */
    protected $optionLookup;

    public function __construct(Image $image, Product $product, OptionsPerProduct $optionLookup, array $config)
    {
        parent::__construct($config);
        $this->image        = $image;
        $this->products     = $product;
        $this->optionLookup = $optionLookup;
    }

    public function indexAction(): ModelInterface
    {
        $this->ajaxAction();
        $params = $this->params()->fromRoute();
        $queryParams = $this->params()->fromQuery();
        if (isset($queryParams['page'])) {
            unset($queryParams['page']);
        }
        if ($queryParams === ['cost' => 'all']) {
            $products = $this->image->fetchAllProductsByMultiColumns(
                true,
                ['i.categoryTitle' => $params['category']]
            );
        } else {
            $products = $this->optionLookup->productSearch(null, $params['category'], $queryParams);
            $products->setCurrentPageNumber($this->params()->fromQuery('page', '1'));
        }
        $this->view->setVariables([
            'products' => $products,
            'category' => $params['category'],
        ]);
        return $this->view;
    }
}
