<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\BaseForm;
use App\Form\Fieldset\ThemeFieldset;
use App\Model\Theme;

final class ThemeSettingsForm extends BaseForm
{
    /** @var Theme $themes */
    protected $themes;

    public function __construct(Theme $theme, array $options = [])
    {
        parent::__construct('themeSettingsForm', $options);
        $this->themes = $theme;
    }

    public function init()
    {
        $data = $this->themes->getConfig();
        foreach ($data as $theme) {
            $fieldset = $this->getFormFactory()->getFormElementManager()->get(ThemeFieldset::class);
            $fieldset->setName($theme['name']);
            $fieldset->populateValues($theme);
            $this->add($fieldset);
        }
    }
}
