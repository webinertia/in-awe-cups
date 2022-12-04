<?php
namespace Store\Db\TableGateway;
use App\Db\TableGateway\TableGateway;
use App\Db\TableGateway\GatewayTrait;
use Laminas\Db\ResultSet\ResultSet;
use Store\Model\Category;

class ProductsByCategoryTable extends TableGateway
{
    use GatewayTrait;

    // public function __construct($table, $container)
    // {
    //     parent::__construct($table, $container);
    //     $resultSet = new ResultSet();
    //     $resultSet->setArrayObjectPrototype(new Category($this, $container));
    //     $this->resultSetPrototype = $resultSet;
    //     $this->initialize();
    // }
}