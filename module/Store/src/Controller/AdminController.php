<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\Category;
use Store\Model\Product;

class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var Category $category */
    protected $category;
    /** @var Product $product */
    protected $product;

    public function __construct(Category $category, Product $product, array $config)
    {
        parent::__construct($config);
        $this->category = $category;
        $this->product  = $product;
    }
    public function overviewAction(): ModelInterface
    {
        $this->view->setTerminal(true);

        return $this->view;
    }

    public function indexAction(): ModelInterface
    {
        return new ViewModel();
    }

    public function managerAction(): ModelInterface
    {
        $this->ajaxAction();

        $data = $this->category->fetchAll()->toArray();
        $this->view->setVariable('data', $data);
        return $this->view;
    }

    public function getResourceId(): string
    {
        return 'admin';
    }
}
