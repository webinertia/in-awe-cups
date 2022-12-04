<?php

declare(strict_types=1);

namespace Store\Form;

use App\Form\BaseForm;
use Store\Form\Fieldset\SearchFieldset;

final class SearchForm extends BaseForm
{
    public function __construct($name = 'search-form', ?array $options = null)
    {
        parent::__construct($name, []);
    }

    public function init(): void
    {
        $this->add([
            'name' => 'search-info',
            'type' => SearchFieldset::class,
        ]);
    }
}
