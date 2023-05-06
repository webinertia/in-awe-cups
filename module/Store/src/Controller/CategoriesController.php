<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Filter\TitleToLabel;
use Laminas\Filter\FilterPluginManager;
use Laminas\Filter\UpperCaseWords;
use Laminas\Navigation\Navigation;
use Laminas\View\Model\ModelInterface;
use Store\Model\Category;
use Store\Model\Product;

final class CategoriesController extends AbstractAppController
{
    /** @var UpperCaseWords $ucwFilter */
    protected $ucwFilter;
    /** @var Category $category */
    protected $category;
    /** @var Product $product */
    protected $product;
    /** @var TitleToLabel $titleToLabelFilter */
    protected $titleToLabelFilter;

    public function __construct(FilterPluginManager $filterManager, Category $category, Product $product, array $config)
    {
        parent::__construct($config);
        $this->ucwFilter = $filterManager->get(UpperCaseWords::class);
        $this->titleToLabelFilter = $filterManager->get(TitleToLabel::class);
        $this->category  = $category;
        $this->product   = $product;
    }

    // all this needs to change so that request are sent to the products controller with all or product title as params
    public function indexAction(): ModelInterface
    {
        // get the service
        $navigation = $this->getService(Navigation::class);
        // get the page so we can set it as active, even though this is not where we are it *should appear* this is where we are
        $page         = $navigation->findOneBy('label', 'Shop');
        $category     = $this->params()->fromRoute('category', 'all');
        $showHeader   = $this->params('showHeader', false);
        $page->active = $this->params('setActive', true);
        // this should only be false when the request is forwarded from the hoem page
        $this->view->setVariable('showHeader', $this->params('showHeader', true));
        switch ($category) {
            case 'all':
                $this->view->setVariables([
                    'headerTitle'    => 'Shopping All Categories',
                    'categories'     => $this->category->fetchAllWithProductCountAndImages(false, false),
                    'showCategories' => true,
                    'showProducts'   => false,
                ]);
                break;
            case 'bundles':
                $this->view->setVariables([
                    'headerTitle'    => 'Shopping ' . $this->titleToLabelFilter->filter($category),
                    'categories'     => $this->category->fetchBundles(true, true),
                    'showCategories' => true,
                    'showProducts'   => false,
                ]);
            break;
            default:
                $this->view->setVariables([
                    'headerTitle'    => 'Shopping ' . $this->titleToLabelFilter->filter($category),
                    'products'       => $this->category->fetchByTitleWithAllProducts($category),
                    'showCategories' => false,
                    'showProducts'   => true,
                ]);
                break;
        }
        return $this->view;
    }

    /** @deprecated */
    public function categoryAction(): ModelInterface
    {
        $category   = $this->params()->fromRoute('category', 'all');
        $showHeader = $this->params('showHeader', false);
        // this should only be false when the request is forwarded from the hoem page
        $this->view->setVariable('showHeader', $this->params('showHeader', true));
        switch ($category) {
            case 'all':
                $this->view->setVariables([
                    'headerTitle'    => 'Shopping All Categories',
                    'categories'     => $this->category->fetchAllWithProductCountAndImages(false, false),
                    'showCategories' => true,
                    'showProducts'   => false,
                ]);
                break;
            case 'bundles':
                $this->view->setVariables([
                    'headerTitle'    => 'Shopping ' . $this->titleToLabelFilter->filter($category),
                    'categories'     => $this->category->fetchBundles(true, true),
                    'showCategories' => true,
                    'showProducts'   => false,
                ]);
            break;
            default:
                $this->view->setVariables([
                    'headerTitle'    => 'Shopping ' . $this->titleToLabelFilter->filter($category),
                    'products'       => $this->category->fetchByTitleWithAllProducts($category),
                    'showCategories' => false,
                    'showProducts'   => true,
                ]);
                break;
        }
        return $this->view;
    }
}
