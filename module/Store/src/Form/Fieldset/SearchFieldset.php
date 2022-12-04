<?php

declare(strict_types=1);

namespace Store\Form\Fieldset;

use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Filter\HtmlEntities;
use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;

class SearchFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @return void */
    public function __construct(string $name = 'search', ?array $options = null)
    {

    }

    public function init(): void
    {

    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
