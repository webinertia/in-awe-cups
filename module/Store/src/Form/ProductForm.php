<?php

namespace Store\Form;

use App\Form\BaseForm;
use Store\Form\Fieldset\ImageUpload;
use Store\Form\Fieldset\ProductInfo;

class ProductForm extends BaseForm
{
    public function init()
    {
        $this->add([
            'name' => 'image-data',
            'type' => ImageUpload::class,
        ])->add([
            'name' => 'product-data',
            'type' => ProductInfo::class,
        ]);
    }
}
