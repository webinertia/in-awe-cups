<?php

/**
 * NOT USED AT THIS TIME !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\Cart;
use Store\Model\CartAwareTrait;
use Store\Model\Product;

class IndexController extends AbstractAppController
{
    use CartAwareTrait;

    public function __construct(Cart $cart, array $config)
    {
        parent::__construct($config);
        $this->cart = $cart;
    }

    public function indexAction(): ModelInterface
    {

        $cartData    = [];
        $products    = $this->getService(Product::class);
        $allProducts = $products->fetchAll();
        foreach ($allProducts as $entry) {
            $cartData[] = $entry->getArrayCopy();
        }
        $this->cart->setItems($cartData);
        $view = new ViewModel();
        $view->setVariable('currentItems', $cartData);
        return $view;
    }

    public function viewOrderAction(): ModelInterface
    {
        return $this->view;
    }

    public function addItemAction(): ModelInterface
    {
        return $this->view;
    }
}
