<?php

declare(strict_types=1);

namespace Store\Api\Form;

use Dojo\Form\Element\Editor;
use Dojo\Form\Element\ValidationTextBox;
use Dojo\Form;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\Validator\StringLength;
use Store\Model\Category;

class ApiCategoryForm extends Form
{
    /** @var Category $category */
    protected $category;
    /** @var array<mixed> $categoryValueOptions */
    protected $categoryValueOptions;

    public function __construct(Category $category, $name = 'apiCategoryForm', $options = [])
    {
        parent::__construct($name, $options);
        $this->category = $category;
        $this->categoryValueOptions = $this->category->fetchSelectValueOptions();
    }

    public function init(): void
    {
        $this->add([
            'name' => 'title',
            'type' => Hidden::class,
        ])->add([
            'name' => 'label',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'placeholder' => 'Category Name:',
                'data-dojo-props' => 'validator:dojox.validate.isText, constraints:{minLength:1, maxLength:255}, invalidMessage:\'Category name must be between 1 and 255 Characters\'',
            ],
            'options' => [
                'label' => 'Name:',
            ],
        ])->add([
            'id'   => 'parentCategoryId',
            'name' => 'parentId',
            'type' => Select::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/Select',
            ],
            'options' => [
                'empty_option'  => 'No Parent',
                'value_options' => $this->categoryValueOptions,
            ],
        ])->add([
            'name' => 'active',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Active ',
            ],
        ])->add([
            'name' => 'isBundle',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Product Bundle ',
            ],
        ])->add([
            'name' => 'onHome',
            'type' => Checkbox::class,
            'attributes' => [
                'data-dojo-type' => 'dijit/form/CheckBox',
            ],
            'options' => [
                'label' => 'Show On Home Page? ',
            ],
        ])->add([
            'name' => 'description',
            'type' => Editor::class,
            'attributes' => [
                'id' => 'apiCategoryEditor',
                'data-dojo-props' => 'extraPlugins:
                [
                    \'foreColor\',
                    \'hiliteColor\'
                ]',
            ],
        ]);
        $this->addSubmit();
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name'        => 'parentId',
                'required'    => false,
                'allow_empty' => true,
                'filters'     => [
                    ['name' => ToInt::class],
                    ['name' => ToNull::class],
                ],
            ],
            [
                'name'     => 'isBundle',
                'required' => true,
                'filters'  => [
                    ['name' => ToInt::class],
                    ['name' => ToNull::class],
                ],
            ],
        ];
    }
}
