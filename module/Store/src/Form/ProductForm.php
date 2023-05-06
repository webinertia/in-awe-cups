<?php

namespace Store\Form;

use Dojo\Form;
use Store\Form\Fieldset\ImageUpload;
use Store\Form\Fieldset\ProductInfo;

class ProductForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'file-data',
            'type' => ImageUpload::class,
        ])->add([
            'name' => 'product-data',
            'type' => ProductInfo::class,
        ]);
    }
}
