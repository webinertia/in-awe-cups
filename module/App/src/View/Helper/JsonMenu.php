<?php

declare(strict_types=1);

namespace App\View\Helper;

use Laminas\Json\Json;
use Laminas\Navigation\Navigation;
use Laminas\View\Helper\AbstractHelper;

class JsonMenu extends AbstractHelper
{
    public function __invoke(Navigation $navigation)
    {
        return Json::encode($navigation->toArray(), Json::TYPE_ARRAY);
    }
}
