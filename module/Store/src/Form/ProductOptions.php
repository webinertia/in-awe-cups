<?php

declare(strict_types=1);

namespace Store\Form;

use Dojo\Form;
use Laminas\Filter\StringToLower;
use Laminas\Form\Element\Hidden;
use Store\Form\Element\OptionCheckbox;
use Store\Model\ProductOptions as Model;

class ProductOptions extends Form
{
    /**@var Model $model */
    protected $model;

    public function __construct($name = 'optionsForm', $options = [])
    {
        parent::__construct($name, $options);
        $this->setOptions($options);
    }

    public function init()
    {
        $options = $this->getOptions();
        $manager = $this->getFormFactory()->getFormElementManager();
        $optionCheckBox = $manager->build(
            OptionCheckbox::class,
            [
                'name' => \strtolower($options['optionGroup']) . '_Options',
                'productId'   => $options['productId'],
                'category'    => $options['category'],
                'optionGroup' => $options['optionGroup'],
                'idAsValue'   => true,
            ]
        );
        $optionCheckBox->setOptions([
            'label' => 'Select all that apply',
            'value_options' => $optionCheckBox->getInitialValues(),
        ]);
        $this->add([
            'name' => 'productId',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'optionGroup',
            'type' => Hidden::class,
        ]);
        $this->add($optionCheckBox);
    }
}
