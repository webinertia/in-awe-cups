<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\ContactMessage;
use Laminas\Captcha\Image;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\Validator\StringLength;

class ContactForm extends Form implements InputFilterAwareInterface
{
    /** @var array<mixed> $appSettings */
    protected $appSettings;
    /**
     * @param string $name
     * @param array $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(array $settings, $options = [])
    {
        $this->appSettings = $settings;
        parent::__construct('contact');
        parent::setOptions($options);
        $this->setObject(new ContactMessage());
    }

    public function init(): void
    {
        $this->add([
            'name'    => 'fullName',
            'type'    => Text::class,
            'options' => ['label' => 'Full Name'],
        ]);
        $this->add([
            'name'    => 'email',
            'type'    => Email::class,
            'options' => ['label' => 'Email'],
        ]);
        $this->add([
            'name'    => 'message',
            'type'    => Textarea::class,
            'options' => ['label' => 'Message'],
        ]);
        if ($this->appSettings['security']['enable_captcha']) {
            $this->add([
                'name'    => 'captcha',
                'type'    => Captcha::class,
                'options' => [
                    'label'   => 'Rewrite Captcha text:',
                    'captcha' => new Image([
                        'name'           => 'myCaptcha',
                        'messages'       => [
                            'badCaptcha' => 'incorrectly rewritten image text',
                        ],
                        'wordLen'        => 5,
                        'timeout'        => 300,
                        'font'           => $_SERVER['DOCUMENT_ROOT'] . '/fonts/arbli.ttf',
                        'imgDir'         => $_SERVER['DOCUMENT_ROOT'] . '/modules/app/captcha/',
                        'imgUrl'         => $this->appSettings['server']['captcha_path'],
                        'lineNoiseLevel' => 4,
                        'width'          => 200,
                        'height'         => 70,
                    ]),
                ],
            ]);
        }
        $this->add([
            'name'       => 'submit',
            'type'       => Submit::class,
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name'       => 'fullName',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'email',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'message',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 2000,
                        ],
                    ],
                ],
            ],
        ];
    }
}
