<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\BaseForm;
use App\Form\Fieldset\AppSettingsFieldset;

class SettingsForm extends BaseForm
{
    /** @var array $settings */
    protected $settings;
    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(array $options = [], array $settings = [])
    {
        parent::__construct('settingForm', $options);
        parent::setOptions($options);
        $this->settings = $settings;
    }

    public function init(): void
    {
        $this->setAttributes(
            [
                'id' => 'settingForm'
            ]
        );
        $this->add([
            'type' => AppSettingsFieldset::class,
        ]);
    }
}
