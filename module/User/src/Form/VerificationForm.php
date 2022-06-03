<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Form;
use RuntimeException;

final class VerificationForm extends Form
{
    /**
     * @param string $name
     * @param array $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct($name, $options = null)
    {
        try {
            parent::__construct('VerificationForm');
            if (! empty($options)) {
                parent::setOptions($options);
            }
        } catch (RuntimeException $e) {
        }
    }
}
