<?php
namespace Store\Controller;
use Application\Controller\AbstractController;
use Laminas\Permissions\Acl\Acl;
use Store\Shipping\ShippingManager;
class ShippingController extends AbstractController
{
    /**
     * @var \Store\Shipping\ShippingManager $shippingManager
     */
    protected $shippingManager;
    /**
     * @var \Laminas\Permissions\Acl\Acl $acl
     */
    public function __construct(Acl $acl, ShippingManager $shippingManager)
    {
        $this->acl = $acl;
        $this->shippingManager = $shippingManager;
    }
    public function indexAction()
    {
        return $this->view;
    }
}