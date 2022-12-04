<?php

declare(strict_types=1);

namespace App\Form\Fieldset;

use App\Model\Setting;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Filter\StringTrim;
use Laminas\Validator\StringLength;

final class ThemeFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @inheritDoc */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);
        parent::setOptions($options);
    }

    public function init()
    {
        $this->getHydrator();
        $this->setObject(new Setting([], Setting::ARRAY_AS_PROPS));
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name'    => 'active',
            'type'    => Checkbox::class,
            'options' => ['label' => 'Active Theme'],
        ]);
        $this->add([
            'name'    => 'name',
            'type'    => Text::class,
            'options' => ['label' => 'Theme Name'],
        ]);
        $this->add([
            'name'    => 'fallback',
            'type'    => Text::class,
            'options' => ['label' => 'Fallback Theme'],
        ]);
        $this->add([
            'name'    => 'has_layout',
            'type'    => Checkbox::class,
            'options' => ['label' => 'Does this theme provide a layout?'],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            // 'name' => [
            //     'filters' => [
            //         ['name' => StringTrim::class]
            //     ],
            //     'validators' => [
            //         [
            //             'name' => StringLength::class,
            //             'options' => [
            //                 'min' => 1,
            //                 'max' => 5,
            //             ],
            //         ],
            //     ],
            // ],
        ];
    }
}
