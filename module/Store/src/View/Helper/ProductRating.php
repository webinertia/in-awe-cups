<?php

declare(strict_types=1);

namespace Store\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function sprintf;

class ProductRating extends AbstractHelper
{
    /** @inheritDoc */
    public function __invoke(?int $count = 0, $iconClass = 'mdi-star', $iconColor = 'text-dark')
    {
        // $html = '<div class="text-primary">
        //             <i class="far fa-star"></i>
        //             <i class="far fa-star"></i>
        //             <i class="far fa-star"></i>
        //             <i class="far fa-star"></i>
        //             <i class="far fa-star"></i>
        //         </div>';
        $html = '<div class="' . $iconColor . '">';
        $icon = '<i class="mdi '. $iconClass . '"></i>';
        for ($i=0; $i < $count; $i++) {
            $html .= "\n$icon";
        }
        $html .= '</div';
        return $html;
    }
}
