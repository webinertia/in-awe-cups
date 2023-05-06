<?php

declare(strict_types=1);

namespace App\Form\Fieldset;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

final class AppSettingsFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param string $name
     * @param array $options
     * @return void
     */
    public function __construct($name = 'app_settings', $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $server   = new Fieldset('server');
        $log      = new Fieldset('log');
        $seo      = new Fieldset('seo');
        $view     = new Fieldset('view');
        $email    = new Fieldset('email');
        $security = new Fieldset('security');
        $server->add([
            'name'    => 'display_time_format',
            'type'    => Text::class,
            'options' => [
                'label'              => 'Display time Format',
            ],
        ])->add([
            'name'    => 'log_errors',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Error Logging?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])->add([
            'name'    => 'enable_translation',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Translation?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ]);
        $seo->add([
            'name'    => 'key_words',
            'type'    => Text::class,
            'options' => ['label' => 'Key Words, comma separated'],
        ])
        ->add([
            'name'    => 'description',
            'type'    => Text::class,
            'options' => ['label' => 'Website Description'],
        ]);
        $view->add([
            'name'    => 'site_name',
            'type'    => Text::class,
            'options' => ['label' => 'Site Name'],
        ])
        ->add([
            'name'    => 'copyright_link',
            'type'    => Text::class,
            'options' => ['label' => 'Copyright Link'],
        ])
        ->add([
            'name'    => 'copyright_text',
            'type'    => Text::class,
            'options' => ['label' => 'Copyright Text'],
        ])
        ->add([
            'name'    => 'footer_text',
            'type'    => Text::class,
            'options' => ['label' => 'Footer Text'],
        ])
        ->add([
            'name'    => 'show_breadcrumbs',
            'type'    => Checkbox::class,
            'options' => ['label' => 'Show Breadcrumbs?'],
        ]);
        $email->add([
            'name'    => 'contact_form_email',
            'type'    => Text::class,
            'options' => ['label' => 'Contact Form Address'],
        ])
        ->add([
            'name'    => 'smtp_sender_address',
            'type'    => Text::class,
            'options' => ['label' => 'SMTP Sender Address'],
        ])
        ->add([
            'name'    => 'smtp_sender_password',
            'type'    => Text::class,
            'options' => ['label' => 'SMTP Sender Password'],
        ])
        ->add([
            'name'    => 'enable_contact_form',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Contact Form?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ]);
        $security->add([
            'name'    => 'enable_login',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Member Login?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'enable_registration',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Member Registration?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'enable_captcha',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Captcha?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ]);
        $this->add($server)
        ->add($log)
        ->add($seo)
        ->add($view)
        ->add($email)
        ->add($security);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'server'   => [
                'log_errors'         => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_translation' => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'seo'      => [
                'key_words'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'description' => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'view'     => [
                'site_name'        => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'copyright_link'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'copyright_text'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'footer_text'      => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'show_breadcrumbs' => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'email'    => [
                'contact_form_email'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'smtp_sender_address'  => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'smtp_sender_password' => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_contact_form'  => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'security' => [
                'enable_login'        => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_registration' => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_captcha'      => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
        ];
    }
}
