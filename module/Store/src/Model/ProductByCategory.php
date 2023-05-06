<?php
declare(strict_types=1);

namespace Store\Model;

use App\Model\AbstractModel;
use App\Model\ModelTrait;
use Store\Db\TableGateway\ProductsByCategoryTable;

class ProductByCategory extends AbstractModel
{
    use ModelTrait;

    public function __construct(?ProductsByCategoryTable $gateway = null)
    {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
    }
}