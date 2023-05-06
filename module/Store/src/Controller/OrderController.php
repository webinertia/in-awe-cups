<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Store\Model\Cart;
use Store\Model\Order;

final class OrderController extends AbstractAppController
{
    /** @var Order $order */
    protected $order;
    public function __construct(Order $order, array $config)
    {
        parent::__construct($config);
        $this->order = $order;
    }
}
