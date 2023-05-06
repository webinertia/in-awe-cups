<?php

declare(strict_types=1);

namespace Store\Form\Fieldset;

use Laminas\Form\Fieldset;

final class ProductOptions extends Fieldset
{
    public function __construct($name = 'productOptions',  $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {

    }
}
