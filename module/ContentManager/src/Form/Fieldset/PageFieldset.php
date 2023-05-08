<?php

declare(strict_types=1);

namespace ContentManager\Form\Fieldset;

use App\Form\FormInterface;
use ContentManager\Model\Page;
use Laminas\Filter\StringTrim;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Dojo\Form\Element\TextBox;
use Dojo\Form\Element\Editor;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

final class PageFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var Page $page */
    protected $page;
    /** @return void */
    public function __construct(Page $page, ?array $options = null)
    {
        $this->page = $page;
        parent::__construct('page-data');
        $this->setAttribute('id', 'page-data');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    public function init(): void
    {
        $this->setUseAsBaseFieldset(true);
        $this->setHydrator(new ArraySerializableHydrator());
        $this->setObject($this->page);
        if ($this->options['mode'] === FormInterface::EDIT_MODE) {
            $this->add([
                'name' => 'id',
                'type' => Hidden::class,
            ]);
        }
        $this->add([
            'name'    => 'label',
            'type'    => TextBox::class,
            'options' => ['label' => 'Page Label (Will show in the menu)'],
        ])
        ->add([
            'name'    => 'showOnLandingPage',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Show on Landing Page',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'order',
            'type'    => Number::class,
            'options' => ['label' => 'Order - The order in which the page will be shown'],
        ])
        ->add([
            'name' => 'content',
            'type' => Editor::class,
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'id'                => [
                'required' => false,
                'filters'  => [
                    ['name' => ToInt::class],
                ],
            ],
            'showOnLandingPage' => [
                'required' => false,
                'filters'  => [
                    ['name' => ToInt::class],
                ],
            ],
            'order'             => [
                'required' => false,
                'filters'  => [
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class],
                    ['name' => ToNull::class],
                ],
            ],
        ];
    }
}
