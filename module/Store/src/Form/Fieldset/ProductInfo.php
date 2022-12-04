<?php

declare(strict_types=1);

namespace Store\Form\Fieldset;

use App\Form\Fieldset\FieldsetTrait;
use Dojo\Form\Element\ComboBox;
use Dojo\Form\Element\DateTextBox;
use Dojo\Form\Element\Editor;
use Dojo\Form\Element\TextBox;
use Laminas\Filter\StringTrim;
use Laminas\Filter\HtmlEntities;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;
use Store\Model\Category;

use function date;

class ProductInfo extends Fieldset implements InputFilterProviderInterface
{
    use FieldsetTrait;

    /** @var Category $category */
    protected $category;

    public function __construct(Category $category, ?array $appSettings = [])
    {
        $this->category = $category;
        $this->categoryValueOptions = $this->category->fetchSelectValueOptions();
        parent::__construct($name = null, $appSettings);
    }

    public function init()
    {
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'userId',
            'type' => Hidden::class,
        ]);
        // createdDate autogen timestamp
        $this->add([
            'name' => 'createdDate',
            'type' => Hidden::class,
            'attributes' => [
                'value' => date($this->getOptions()['server']['time_format']),
            ],
        ]);
        $this->add([
            'name' => 'label',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product Name:',
            ],
        ]);
        $this->add([
            'name' => 'categoryId',
            'type' => Select::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/Select',
            ],
            'options' => [
                'empty_option'  => 'Category Required!',
                'value_options' => $this->categoryValueOptions,
            ],
        ]);
        // cost decimal 10,2
        $this->add([
            'id'   => 'cost',
            'name' => 'cost',
            'type' => TextBox::class,
            'attributes' => [
                'Placeholder' => 'Product Cost: (10.00)'
            ],
        ]);
        // weight decimal
        $this->add([
            'name' => 'weight',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Shipping weight: ex 10.5',
            ],
        ]);
        // manufacturer text
        $this->add([
            'name' => 'manufacturer',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product Manufacturer',
                'value'       => 'In Awe Cups & More',
            ],
        ]);
        // sku text
        $this->add([
            'name' => 'sku',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product SKU:'
            ],
        ]);
        // discount decimal 3,2
        $this->add([
            'name' => 'discount',
            'required' => false,
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Discount: ex 25 = 25%',
            ],
        ]);
        // saleStartDate datepicker
        $this->add([
            'id'   => 'saleStartDate',
            'name' => 'saleStartDate',
            'type' => DateTextBox::class,
            'attributes' => [
                'placeholder' => 'Sale Start Date:',
            ],
        ]);
        // saleEndDate datepicker
        $this->add([
            'id'   => 'saleEndDate',
            'name' => 'saleEndDate',
            'type' => DateTextBox::class,
            'attributes' => [
                'placeholder' => 'Sale End Date:',
            ],
        ]);
        // active bool checkbox
        $this->add([
            'name' => 'active',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Active ',
            ],
        ]);
        // onSale bool checkbox
        $this->add([
            'name' => 'onSale',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => 'On Sale ',
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => Editor::class,
        ]);
    }
    public function getInputFilterSpecification()
    {
        return [
            'label' => [
                'required' => true,
                'filters'  => [
                    ['name' => StringTrim::class],
                    ['name' => HtmlEntities::class],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 2,
                        ],
                    ],
                ],
            ],
        ];
    }
}
