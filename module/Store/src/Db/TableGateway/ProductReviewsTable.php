<?php
namespace Store\Db\TableGateway;
use Application\Db\TableGateway\TableGatewayTrait;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Store\Model\ProductReview;
class ProductReviewsTable extends TableGateway
{
    use TableGatewayTrait;
    use TableGatewayTrait;
    public function __construct($table, $container)
    {
        parent::__construct($table, $container);
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new ProductReview($this, $container));
        $this->resultSetPrototype = $resultSet;
        $this->initialize();
    }
}