<?php

declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelTrait;

class Order extends AbstractGatewayModel
{
    use ModelTrait;
}