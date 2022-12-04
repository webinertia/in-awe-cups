<?php
namespace Store\Shipping;
use Store\Shipping\Services\Fedex;
use Store\Shipping\Services\Ups;
use Store\Shipping\Services\Usps;
class ShippingManager
{
    /**
     *
     * @var \Store\Shipping\Services\Fedex $fedex
     */
    protected $fedex;
    /**
     *
     * @var \Store\Shipping\Services\Ups $ups
     */
    protected $ups;
    /**
     *
     * @var \Store\Shipping\Services\Usps $usps
     */
    protected $usps;
    /**
     *
     * @param Fedex $fedex
     * @param Ups $ups
     * @param Usps $usps
     * @return void
     */
    public function __construct(Fedex $fedex, Ups $ups, Usps $usps)
    {
        $this->fedex = $fedex;
        $this->ups = $ups;
        $this->usps = $usps;
    }
}