<?Php

declare(strict_types=1);

namespace App\Form;

use Dojo\Form\Element\Submit;
use Dojo\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class BaseForm extends Form implements InputFilterProviderInterface
{
    /** @inheritDoc */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name);
        parent::setOptions($options);
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
