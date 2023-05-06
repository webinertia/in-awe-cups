<?php

declare(strict_types=1);

namespace Store\Api\Form;

use App\Form\BaseForm;

use App\Form\Fieldset\FieldsetTrait;
use DateTimeInterface;
use Dojo\Form\Element\CurrencyTextBox;
use Dojo\Form\Element\DateTextBox;
use Dojo\Form\Element\Editor;
use Dojo\Form\Element\TextBox;
use Dojo\Form\Element\ValidationTextBox;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;
use Store\Form\Fieldset\ImageUpload;
use Store\Model\Category;
use User\Form\Element\UserId;

class ApiProductForm extends BaseForm
{
    /** @var array<mixed> $bundleValueOptions */
    protected $bundleValueOptions;
    /** @var Category $category */
    protected $category;
    /** @var array<mixed> $categoryValueOptions */
    protected $categoryValueOptions;
    /** @var array<string, mixed> $config */
    protected $config;

    public function __construct(?Category $category, ?array $config, $name = 'apiProductForm', $options = [])
    {
        $this->category = $category;
        $this->config   = $config;
        $options['server']['time_format'] = DateTimeInterface::RFC3339;
        parent::__construct($name, $options);
        $this->categoryValueOptions = $this->category->fetchSelectValueOptions(false);
        $this->bundleValueOptions   = $this->category->fetchSelectValueOptions(false, true);
    }

    public function init(): void
    {
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'userId',
            'type' => UserId::class,
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
            'type' => ValidationTextBox::class,
            'attributes' => [
                'placeholder'     => 'Product Name:',
                'data-dojo-props' => 'validator:dojox.validate.isText, constraints:{minLength:1, maxLength:255}, invalidMessage:\'Must be between 1 and 255 characters.\'',
            ],
            'options' => [
                'label' => 'Product Name:'
            ],
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
            'name' => 'bundleLabel',
            'type' => Select::class,
            'attributes' => [
                'required' => false,
                'data-dojo-type' => 'dijit/form/Select',
            ],
            'options' => [
                'empty_option'  => 'Bundle',
                'value_options' => $this->bundleValueOptions,
            ],
        ]);
        // cost decimal 10,2
        $this->add([
            'id'   => 'cost',
            'name' => 'cost',
            'type' => CurrencyTextBox::class,
            'attributes' => [
                'required'    => true,
                'Placeholder' => 'Product Cost: (10.00)',
            ],
            'options' => [
                'label' => 'Cost:'
            ],
        ]);
        // weight decimal
        $this->add([
            'name' => 'weight',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'required'    => true,
                'placeholder' => 'Shipping weight: ex 10.5',
            ],
            'options' => [
                'label' => 'Weight:',
            ],
        ]);
        // manufacturer text
        $this->add([
            'name' => 'manufacturer',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product Manufacturer',
                'value'       => 'In Awe Cups & More',
                'disabled'    => true,
            ],
            'options' => [
                'label' => 'Manufacturer:',
            ],
        ]);
        // sku text
        $this->add([
            'name' => 'sku',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product SKU:'
            ],
            'options' => [
                'label' => 'Product SKU:',
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => Editor::class,
            'attributes' => [
                'id' => 'productEditor',
                'data-dojo-props' => 'extraPlugins:
                [
                    \'foreColor\',
                    \'hiliteColor\'
                ]',
            ],
        ]);
        $this->addSubmit(100);
    }
}
