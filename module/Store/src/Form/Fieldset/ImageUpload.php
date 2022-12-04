<?php

declare(strict_types=1);

namespace Store\Form\Fieldset;

use App\Form\Fieldset\FieldsetTrait;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\FilesSize;

final class ImageUpload extends Fieldset implements InputFilterProviderInterface
{
    use FieldsetTrait;

    public function __construct($name = 'image-upload', $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $options = $this->getOptions();

        $this->add([
            'name' => 'userId',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'productId',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'categoryId',
            'type' => Hidden::class,
        ]);
        // createdDate autogen timestamp
        $this->add([
            'name' => 'uploadedTime', // move this to a db listener
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'images',
            'type' => File::class,
            'attributes' => [
                'id'   => 'imageUploader',
                'multiple' => true,
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            // 'images' => [
            //     'required' => false,
            //     'validators' => [
            //         ['name' => IsImage::class],
            //         [
            //             'name'    => FilesSize::class,
            //             'options' => [
            //                 'min' => '1KB',
            //                 'max' => '250KB',
            //             ],
            //         ],
            //         [
            //             'name'    => ImageSize::class,
            //             'options' => [
            //                 'maxWidth'  => 1032,
            //                 'maxHeight' => 1032,
            //             ],
            //         ],
            //     ],
            // ],
        ];
    }
}
