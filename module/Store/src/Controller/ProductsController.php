<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Filter\TitleToLabel;
use Laminas\Navigation\Navigation;
use Laminas\Filter\FilterPluginManager;
use Laminas\Filter\UpperCaseWords;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\Category;
use Store\Model\Image;
use Store\Model\Product;
use Store\Model\ProductOptions;
use Store\Model\OptionsPerProduct;

final class ProductsController extends AbstractAppController
{
    /** @var Category $category */
    protected $category;
    /** @var UpperCaseWords $ucwFilter */
    protected $ucwFilter;
    /** @var Product $product */
    protected $product;
    /** @var ProductOptions $productOptions*/
    protected $productOptions;
    /** @var OptionsPerProduct $optionLookup */
    protected $optionLookup;

    /** @var TitleToLabel $titleToLabelFilter */
    protected $titleToLabelFilter;
    /** @var $navigation */
    protected $navigation;
    /** @var Image $image */
    protected $image;

    public function __construct(
        Category $category,
        FilterPluginManager $filterManager,
        Product $product,
        ProductOptions $productOptions,
        OptionsPerProduct $optionLookup,
        Image $image,
        array $config
        ) {
        parent::__construct($config);
        $this->category           = $category;
        $this->titleToLabelFilter = $filterManager->get(TitleToLabel::class);
        $this->ucwFilter          = $filterManager->get(UpperCaseWords::class);
        $this->product            = $product;
        $this->productOptions     = $productOptions;
        $this->optionLookup       = $optionLookup;
        $this->image              = $image;
    }

    // Show all products for a category or show a single product
    public function indexAction(): ModelInterface
    {
        $navigation = $this->getService(Navigation::class);
        // set shop navigation tab as active
        $page = $navigation->findOneBy('label', 'Shop');
        $page->active = true;
        $params = $this->params()->fromRoute();
        $category = $this->titleToLabelFilter->filter($params['category']);
        $this->view->setVariable('showHeader', true);
        switch($params['product']) {
            case 'all':
                $queryParams = $this->getQuery(exclude:['page']);
                $this->view->setVariables([
                    'headerTitle' => 'Shopping ' . $category,
                ]);
                $sidebar = new ViewModel(
                    [
                        'productOptions' => $this->optionLookup->fetchSearchableOptions($params['category']),
                        'category'       => $category,
                        'query'          => $queryParams,
                        'maxPrice'       => $this->product->fetchMaxCost($this->titleToLabelFilter->filter($params['category']), true),
                    ]
                );
                // this only gets rendered for non ajax request
                $sidebar->setTemplate('/store/products/children/product-search-sidebar');
                $this->view->addChild($sidebar, 'sidebar');
                // only products that have been assigned options will show up in the return data
                $products = $this->optionLookup->productSearch(null, $category, $queryParams);
                /** @var Paginator $products */
                if (count($queryParams) > 0) {
                    $pages = $products->getPages();
                    $products->setItemCountPerPage($pages->totalItemCount);
                } else {
                    $products->setItemCountPerPage($this->config['module_settings']['store']['pagination']['items_per_page']);
                }
                $products->setCurrentPageNumber($this->params()->fromQuery('page', 1));
                $this->view->setVariables(
                    [
                        'products' => $products,
                        'category' => $params['category'],
                        'route'    => 'store/product',
                    ]
                );
                if ($this->ajaxAction()) {
                    // we set this as the response for ajax request so that it will only update the html that needs replaced
                    $this->view->setTemplate('/store/products/json-response');
                }
                break;
            default:
                $product = $this->product->fetchDetail($params['product']);
                $this->view->setVariables([
                    'product'     => $product,
                    'options'     => $this->optionLookup->fetchOptionsByProductId($product->id),
                    'images'      => $this->image->fetchProductImagesById($product->id),
                    'headerTitle' => $this->titleToLabelFilter->filter($params['product']),
                ]);
                // override the template so we only show one product, the product detail page
                $this->view->setTemplate('/store/products/detail');
            break;
        }
        return $this->view;
    }
}
