<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Laminas\Stdlib\ArrayUtils;

use const ARRAY_FILTER_USE_KEY;

use function array_filter;
use function array_flip;
use function array_key_exists;
use function in_array;

trait QueryParamsTrait
{
    public function getQuery(?string $param = null, $default = null, ?array $exclude = [])
    {
        if ($param !== null) {
            return $this->request->getQuery($param, $default);
        }

        if ($param === null && $exclude === []) {
            return $this->request->getQuery($param, $default)->toArray();
        }

        if ($exclude !== []) {
            return array_filter($this->request->getQuery($param, $default)->toArray(), function($k) use($exclude) {
                return !in_array($k, $exclude);
            }, ARRAY_FILTER_USE_KEY);
        }
    }
}
