<?php

declare(strict_types=1);

namespace Store\Form;

use App\Filter\PadFloatString;
use Dojo\Form;
use Dojo\Form\Element\ValidationTextBox;
use Dojo\Form\Element\CurrencyTextBox;
use Laminas\Filter\StringTrim;
use Laminas\Filter\ToFloat;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Filter\UpperCaseWords;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Store\Model\Category;

class OptionGroupForm extends Form
{
    /** @var Category $category */
    protected $category;
    /** @var array<mixed> $categoryValueOptions */
    protected $categoryValueOptions;
    public function __construct(Category $category, $name = 'optionGroupForm', $options = [])
    {
        if ($options === null) {
            $options = [];
        }
        parent::__construct($name, $options);
        $this->category = $category;
        $this->categoryValueOptions = $this->category->fetchSelectValueOptions(false);
    }

    public function init(): void
    {
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'category',
            'type' => Select::class,
            'attributes' => [
                'required' => true,
                'data-dojo-type' => 'dijit/form/Select',
            ],
            'options' => [
                'empty_option'  => 'Category Required!',
                'value_options' => $this->categoryValueOptions,
            ],
        ]);
        $this->add([
            'name' => 'optionGroup',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'required' => true,
                'placeholder' => 'Option Group Name: ',
            ],
        ]);
        $this->add([
            'name' => 'option',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'required' => true,
                'placeholder' => 'Option: ',
            ],
        ]);
        // cost decimal 10,2
        $this->add([
            'id'   => 'cost',
            'name' => 'cost',
            'type' => CurrencyTextBox::class,
            'attributes' => [
                'required'    => true,
                'Placeholder' => 'Option Cost: (10.00)',
            ],
        ]);
        $this->addSubmit();
    }
    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name'        => 'id',
                'required'    => false,
                'allow_empty' => true,
                'filters'     => [
                    ['name' => ToInt::class],
                    ['name' => ToNull::class],
                ],
            ],
            [
                'name'     => 'optionGroup',
                'required' => true,
                'filters'  => [
                    ['name' => StringTrim::class],
                    ['name' => UpperCaseWords::class],
                ],
            ],
            [
                'name'     => 'option',
                'required' => true,
                'filters'  => [
                    ['name' => StringTrim::class],
                    ['name' => UpperCaseWords::class],
                ],
            ],
            [
                'name'     => 'cost',
                'required'    => false,
                'allow_empty' => true,
                'filters'  => [
                    ['name' => ToFloat::class],
                    ['name' => ToNull::class],
                ],
            ],
        ];
    }
}
