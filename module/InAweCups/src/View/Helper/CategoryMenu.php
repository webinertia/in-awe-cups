<?php

declare(strict_types=1);

namespace InAweCups\View\Helper;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\Url;
use Store\Model\Category;
use Store\View\Helper\LabelHelper;

use function count;

final class CategoryMenu extends AbstractHelper
{
    /** @var Category $category */
    protected $category;
    /** @var array<mixed> $categories */
    protected $categories;
    /** @var ResultSet $data */
    protected $data;
    protected $url;

    public function __construct(Category $category, LabelHelper $labelHelper, Url $url)
    {
        $this->categories = $category->fetchMenu();
        $this->url = $url;
    }

    public function __invoke(bool $showBundles = false, bool $showEmpty = false)
    {
        $html = '<a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100" data-toggle="collapse" href="#navbar-vertical" style="height: 65px; margin-top: -1px; padding: 0 30px;">
                    <h6 class="m-0">Categories</h6>
                    <i class="mdi mdi-chevron-down text-dark"></i>
                </a>
                <nav id="navbar-vertical" class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0 bg-light" style="width: calc(100% - 30px); z-index: 1;">
                    <div class="navbar-nav w-100 overflow-hidden" style="height: auto">';
                    $count = count($this->categories);
                    for ($i=0; $i < $count; $i++) {
                        $html .= '<a href="'. $this->getView()->url('store/product', ['action' => 'index', 'category' => $this->categories[$i]['title'], 'product' => 'all']).'" class="nav-item nav-link">'.$this->categories[$i]['label'].'</a>';
                        //$html .= '<a href="/store/'.$this->categories[$i]['title'].'" class="nav-item nav-link">'.$this->categories[$i]['label'].'</a>';
                        // if ($this->categories[$i]['children'] !== null) {
                        //     $html .= '<a href="/store/'.$this->categories[$i]['title'].'" class="nav-item nav-link">'.$this->categories[$i]['label'].'</a>';
                        // } elseif ($this->categories[$i]['children'] === null && $this->categories[$i]['parentId'] === null) {
                        //     //$html .= '<a href="/store/'.$this->categories[$i]['title'].'" class="nav-item nav-link">'.$this->categories[$i]['label'].'</a>';
                        // }
                    }
                $html .= '</div>
                </nav>';
                    return $html;
    }
}
