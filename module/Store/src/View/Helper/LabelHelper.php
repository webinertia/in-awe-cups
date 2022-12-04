<?php

declare(strict_types=1);

namespace Store\View\Helper;

use App\Filter\TitleToLabel;
use Laminas\View\Helper\AbstractHelper;

final class LabelHelper extends AbstractHelper
{
    /** @var TitleToLabel $filter */
    protected $filter;

    public function __construct(TitleToLabel $filter)
    {
        $this->filter = $filter;
    }

    public function __invoke(string $label)
    {
        return $this->filter->filter($label);
    }
}
