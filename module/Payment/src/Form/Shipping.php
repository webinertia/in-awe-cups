<?php

declare(strict_types=1);

namespace Payment\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Fieldset;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Submit;
use Laminas\InputFilter\InputFilterProviderInterface;
use Payment\Form\Fieldset\Address;
use Payment\Form\Fieldset\Security;

use function strtolower;

final class Shipping extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        $name = 'shipping';
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $options = $this->getOptions();
        $manager = $this->getFormFactory()->getFormElementManager();
        $this->add($manager->get(Address::class));
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
