<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Upload\UploadAwareInterface;
use Laminas\View\Model\JsonModel;
use Store\Model\Cart;
use Store\Model\CartAwareTrait;
use Store\Model\Category;
use Store\Model\Image;
use Store\Model\Product;

class DataApiController extends AbstractApiController implements UploadAwareInterface
{
    use CartAwareTrait;

    /** @var Cart $cart */
    protected $cart;
    /** @var Category $category */
    protected $category;
    /** @var Image $image */
    protected $image;
    /** @var Order $order */
    protected $order;
    /** @var Product $product */
    protected $product;

    public function __construct(
        Cart $cart,
        Category $category,
        Image $image,
        Product $product,
        array $config
    ) {
        parent::__construct($config);
        $this->cart     = $cart;
        $this->category = $category;
        $this->image    = $image;
        $this->product  = $product;
        $this->view->setTerminal(true);
        $this->view = new JsonModel();
    }

    public function categoryDataAction()
    {
        $data = $this->category->fetchAll()->toArray();
        return new JsonModel($data);
    }
}
