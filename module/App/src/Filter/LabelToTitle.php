<?php

declare(strict_types=1);

namespace App\Filter;

use Laminas\Filter\FilterChain;
use Laminas\Filter\FilterInterface;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;

final class LabelToTitle implements FilterInterface
{
    /** @var FilterChain $filterChain */
    protected $filterChain;

    public function __construct()
    {
        $this->filterChain = new FilterChain();
        $this->filterChain->attach(new StringToLower())->attach(new SeparatorToDash(' '));
    }
    /** @inheritDoc */
    public function filter($value)
    {
        return $this->filterChain->filter($value);
    }
}
