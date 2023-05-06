<?php

declare(strict_types=1);

namespace Store\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Store\Model\Category;

use function sprintf;

final class Categories extends AbstractHelper
{
    /** @var Category $category*/
    protected $category;
    /** @var string $html */
    protected $html;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function __invoke(int $showCount = 6, bool $showImage = true)
    {
        $config = $this->getView()->plugin('config');


    //     $this->html = '<div class="col-lg-4 col-md-6 pb-1">
    //     <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
    //         <p class="text-right">15 Products</p>
    //         <a href="" class="cat-img position-relative overflow-hidden mb-3">
    //             <img class="img-fluid" src="img/cat-1.jpg" alt="">
    //         </a>
    //         <h5 class="font-weight-semi-bold m-0">'..'</h5>
    //     </div>
    // </div>';
    }
}
