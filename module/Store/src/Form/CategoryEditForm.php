<?php

declare(strict_types=1);

namespace Store\Form;

use Store\Form\CategoryForm;

class CategoryEditForm extends CategoryForm
{
    public function init(): void
    {
        parent::init();
        $this->setName('categoryEditForm');
    }
}
