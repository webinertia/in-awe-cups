<?php

declare(strict_types=1);

namespace App\Filter;

use Laminas\Filter\FilterChain;
use Laminas\Filter\FilterInterface;
use Laminas\Filter\UpperCaseWords;
use Laminas\Filter\Word\SeparatorToSeparator;

final class TitleToLabel implements FilterInterface
{
    /** @var FilterChain $filterChain */
    protected $filterChain;

    public function __construct()
    {
        $this->filterChain = new FilterChain();
        $this->filterChain->attach(new SeparatorToSeparator('-', ' '))->attach(new UpperCaseWords());
    }
    public function filter($value)
    {
        return $this->filterChain->filter($value);
    }
}
