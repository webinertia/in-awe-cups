<?php

declare(strict_types=1);

namespace Store\Form;

use App\Form\BaseForm;
use Store\Form\Fieldset\CategoryInfo;
use Store\Form\Fieldset\ImageUpload;

class CategoryForm extends BaseForm
{
    /** @return void */
    public function __construct($name = 'category-form', $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $options = $this->getOptions();
        $this->add([
            'name' => 'image-data',
            'type' => ImageUpload::class,
        ])->add([
            'name' => 'category-data',
            'type' => CategoryInfo::class,
        ]);
    }
}
