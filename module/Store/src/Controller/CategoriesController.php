<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\Filter\FilterPluginManager;
use Laminas\Filter\UpperCaseWords;
use Laminas\View\Model\ModelInterface;
use Store\Model\Category;

final class CategoriesController extends AbstractAppController
{
    /** @var UpperCaseWords $ucwFilter */
    protected $ucwFilter;

    public function __construct(FilterPluginManager $filterManager, Category $category, array $config)
    {
        parent::__construct($config);
        $this->ucwFilter = $filterManager->get(UpperCaseWords::class);
        $this->category  = $category;
    }

    public function indexAction(): ModelInterface
    {
        $this->view->setVariables(['headerTitle' => 'Shop Categories']);
        $category = $this->params()->fromRoute('name', 'all');
        $showHeader = $this->params('showHeader', true);
        //$this->view->setVariables(['headerTitle' => 'Shop ' . $this->ucwFilter->filter($category), 'showHeader' => $showHeader]);
        return $this->view;
    }

    public function categoryAction(): ModelInterface
    {
        $category = $this->params()->fromRoute('name', 'all');
        $showHeader = $this->params('showHeader', true);
        //$this->view->setVariables(['headerTitle' => 'Shop ' . $this->ucwFilter->filter($category), 'showHeader' => $showHeader]);
        return $this->view;
    }
}
