<?php
namespace Store\Controller;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Interop\Container\ContainerInterface;
class ProductsApiController extends AbstractRestfulController
{
    public function get($id)
    {

    }
    public function getList()
    {
        $page = (int) $this->params('page', '1');

    }
}