<?php

declare(strict_types=1);

namespace Store\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Store\Model\Cart as Model;

class Cart extends AbstractHelper
{
    /** @var Model $model */
    protected $model;
    /** @var array<mixed> $params */
    protected $params;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function __invoke(?array $params = null)
    {
        $this->params = $params;
        return $this;
    }

    public function getItemCount(): int
    {
        return $this->model->getItemCount();
    }

    public function getSubTotal(): float
    {
        return $this->model->getSubTotal();
    }

    public function getTotal(): float
    {
        $subTotal = $this->getSubTotal();
        $shipping = $this->getShipping();
        return $subTotal + $shipping;
    }

    public function getShipping(): float
    {
        return $this->model->getShippingCost();
    }
}
