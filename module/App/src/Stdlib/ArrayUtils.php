<?php

declare(strict_types=1);

namespace App\Stdlib;

use Laminas\Stdlib\ArrayUtils as Lib;

use function array_diff_key;
use function array_intersect_key;
use function array_flip;

abstract class ArrayUtils extends Lib
{
    /**
     * credit to Nathan.Fiscaletti@gmail.com
     * as posted on php.net
     * https://www.php.net/manual/en/function.array-slice.php
     * */
    public static function arraySliceAssoc($array, $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * credit to Nathan.Fiscaletti@gmail.com
     * as posted on php.net
     * https://www.php.net/manual/en/function.array-slice.php
     * */
    public static function arraySliceAssocInverse($array, $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }
}
