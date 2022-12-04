<?php

declare(strict_types=1);

namespace InAweCups\View\Helper;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\View\Helper\AbstractHelper;
use Store\Model\Category;
use Store\View\Helper\LabelHelper;

use function count;

final class CategoryMenu extends AbstractHelper
{
    /** @var Category $category */
    protected $category;
    /** @var ResultSet $data */
    protected $data;

    public function __construct(Category $category, LabelHelper $labelHelper)
    {
        $this->categories = $category->fetchAllWithChildren();
    }

    public function __invoke()
    {
        $html = '<a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100" data-toggle="collapse" href="#navbar-vertical" style="height: 65px; margin-top: -1px; padding: 0 30px;">
                    <h6 class="m-0">Categories</h6>
                    <i class="mdi mdi-chevron-down text-dark"></i>
                </a>
                <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0 bg-light" id="navbar-vertical" style="width: calc(100% - 30px); z-index: 1;">
                    <div class="navbar-nav w-100 overflow-hidden" style="height: auto">';
                    $count = count($this->categories);
                    for ($i=0; $i < $count; $i++) {
                        if ($this->categories[$i]['children'] !== null) {
                            $html .= '<div class="nav-item dropdown">
                                        <a href="#" class="nav-link" data-toggle="dropdown">'.$this->categories[$i]['label'].'<i class="mdi mdi-chevron-down float-right mt-1"></i></a>
                                        <div class="dropdown-menu position-absolute bg-secondary border-0 rounded-0 w-100 m-0">';
                                foreach($this->categories[$i]['children'] as $child) {
                                        $html .= '<a href="/store/'.$child['title'].'" class="dropdown-item">'.$child['label'].'</a>';
                                    continue;
                                }
                                $html .= '</div>
                                </div>';
                        } elseif ($this->categories[$i]['children'] == null && $this->categories[$i]['parentId'] === null) {
                            $html .= '<a href="/store/'.$this->categories[$i]['title'].'" class="nav-item nav-link">'.$this->categories[$i]['label'].'</a>';
                        }
                    }
                $html .= '</div>
                </nav>';
                    return $html;
    }
}
