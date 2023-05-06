<?php

declare(strict_types=1);

namespace Store\Form;

use Dojo\Form;
use Store\Form\Fieldset\ImageUpload;

final class UploadForm extends Form
{
    public function __construct($name = 'uploadForm', $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $this->add([
            'name' => 'file-data',
            'type' => ImageUpload::class,
        ]);
        $this->addSubmit();
    }
}
