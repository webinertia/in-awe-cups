<?php

declare(strict_types=1);

namespace Store\Db\TableGateway;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Store\Model\ProductReview;

final class ProductReviewsTable extends TableGateway
{
    use GatewayTrait;

    public function __construct($table, $container)
    {
        parent::__construct($table, $container);
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new ProductReview($this, $container));
        $this->resultSetPrototype = $resultSet;
        $this->initialize();
    }
}