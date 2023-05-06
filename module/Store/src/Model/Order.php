<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\AbstractModel;
use App\Model\ModelTrait;
use Laminas\Db\TableGateway\TableGateway;

class Order extends AbstractModel
{
    use ModelTrait;

    public function __construct(TableGateway $gateway, array $config)
    {
        parent::__construct([], $config);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
    }
}