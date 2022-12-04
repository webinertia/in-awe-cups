<?php

declare(strict_types=1);

namespace Store\Db\TableGateway;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;
use Laminas\Db\ResultSet\ResultSet;

class ProductsTable extends TableGateway
{
    use GatewayTrait;
}