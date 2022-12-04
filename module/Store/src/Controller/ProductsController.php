<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\Filter\FilterPluginManager;
use Laminas\Filter\UpperCaseWords;
use Laminas\View\Model\ModelInterface;
use Store\Model\Product;

final class ProductsController extends AbstractAppController
{
    /** @var UpperCaseWords $ucwFilter */
    protected $ucwFilter;

    public function __construct(FilterPluginManager $filterManager, Product $product, array $config)
    {
        parent::__construct($config);
        $this->ucwFilter = $filterManager->get(UpperCaseWords::class);
        $this->product   = $product;
    }

    public function indexAction(): ModelInterface
    {
        //var_dump($this->ucwFilter);
        return $this->view;
    }

    public function productAction(): ModelInterface
    {
        return $this->view;
    }
}
