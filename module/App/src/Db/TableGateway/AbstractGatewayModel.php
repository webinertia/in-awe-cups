<?php

declare(strict_types=1);

namespace App\Db\TableGateway;

use Laminas\Stdlib\ArrayObject;
use Laminas\Stdlib\Exception;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterAwareTrait;

abstract class AbstractGatewayModel extends ArrayObject implements InputFilterAwareInterface
{
    use InputFilterAwareTrait;

    /** @var InputFilter $inputFilter */
    protected $inputFilter;
    protected $inputFilterClass = InputFilter::class;

    public function __construct(array $data = [])
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }
}
