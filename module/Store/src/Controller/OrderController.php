<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Store\Model\Order;

final class OrderController extends AbstractAppController
{
    /** @var Order $order */
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function indexAction()
    {

    }

    public function viewAction()
    {

    }

    public function listAction()
    {

    }
}
