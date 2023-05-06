<?php

declare(strict_types=1);

namespace Payment\Form\Fieldset;

use Bootstrap\BootstrapInterface;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class Address extends Fieldset implements BootstrapInterface
{
    protected $attributes = ['class' => 'form-group'];
    public function __construct($name = 'billing', $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttributes([
            'id' => $name,
        ]);
    }

    public function init(): void
    {
        $this->add([
            'name' => 'street',
            'type' => Element\Text::class,
            'attributes' => [
                'class' => 'form-input',
            ],
            'options' => [
                'label' => 'Street',
            ],
        ]);
    }
}
