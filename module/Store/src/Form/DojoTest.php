<?php

declare(strict_types=1);

namespace Store\Form;

use App\Form\BaseForm;
use Dojo\Form\Element\Editor;
use Dojo\Form\Element\ComboBox;
use Dojo\Form\Element\MultiFile;
use Laminas\Form\Element\File;
use Dojo\Form\Element\UploaderFileList;

class DojoTest extends BaseForm
{
        /** @return void */
        public function __construct($name = 'category-form', $options = [])
        {
            parent::__construct($name, $options);
        }

        public function init()
        {
            $this->add([
                //'name' => 'images',
                'type' => MultiFile::class,
                'attributes' => [
                    'id'   => 'imageUploader',
                    //'data-dojo-props' => 'label: \'Select Files\'',
                    'multiple' => true,
                ],
            ]);
            $this->add([
                'id' => 'files',
                'type' => UploaderFileList::class,
                'attributes' => [
                    'data-dojo-props' => 'uploaderId: \'imageUploader\'',
                ],
            ]);
            $this->add([
                'name' => 'description',
                'type' => Editor::class,
            ]);
        }
}
