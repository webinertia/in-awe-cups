<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Log\LogEvent;
use Laminas\Navigation\Navigation;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Payment\Service\Gateway;
use Ramsey\Uuid;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Store\Model\Cart;

final class CartController extends AbstractAppController
{
    /** @var Cart $cart */
    protected $cart;
    protected $session;
    /** @var string $customerId */
    protected $customerId;
    protected Gateway $gateway;

    public function __construct(Gateway $gateway, Cart $cart, array $config)
    {
        parent::__construct($config);
        $this->gateway = $gateway;
        $this->cart    = $cart;
    }

    public function indexAction(): ModelInterface
    {
        $navigation = $this->getService(Navigation::class);
        // set shop navigation tab as active
        $page = $navigation->findOneBy('label', 'Shop');
        $page->active = true;
        $this->view->setVariables(
            [
                'products' => $this->cart->getItems()
            ]
        );
        return $this->view;
    }

    /** @deprecated */
    public function checkoutAction(): ModelInterface
    {
        // set shop navigation tab as active
        $navigation = $this->getService(Navigation::class);
        $page = $navigation->findOneBy('label', 'Shop');
        $page->active = true;
        // end navigation
        $layout = $this->layout();
        $layout->setVariables([
            'clientToken'     => $this->gateway->clientToken()->generate(),
            'integrationType' => $this->gateway->getIntegrationType(),
        ]);
        $this->view->setVariables(
            [
                'products'        => $this->cart->getItems(),
                'integrationType' => $this->gateway->getIntegrationType(),
            ]
        );
        return $this->view;
    }

    public function transactionDetailsAction(): ModelInterface
    {
        $id = $this->getQuery('id');
        return $this->view;
    }

    public function paymentSuccessAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }

        return $this->view;
    }

    public function paymentCancelAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }

        return $this->view;
    }

    public function addItemAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            //$this->view = new JsonModel();
        }
        if ($this->request->isPost()) {
            $item = $this->request->getPost()->toArray();
            $item['quantity'] = (int) $item['quantity'];
            $this->cart->addToCart($item);
        }
        $items = $this->cart->getItems();
        return $this->view;
    }

    public function removeItemAction(): ModelInterface
    {
        try {
            $this->ajaxAction();
            $params = $this->getQuery();
            if ($this->cart->removeItem((int) $params['id'], $params['cartId'])) {
                $this->view->setVariables([
                    'products' => $this->cart->getItems(),
                ]);
            } else {
                $this->response->setStatusCode(500);
            }
        } catch (\Throwable $th) {
            $this->getEventManager()->trigger(
                LogEvent::NOTICE,
                $th->getMessage(),
                [
                    'trace' => $th->getTraceAsString(),
                ]
            );
            throw $th;
        }
        return $this->view;
    }

    public function emptyCartAction(): ModelInterface
    {
        $this->ajaxAction();
        $this->cart->emptyCart();
        $this->view->setVariables(
            [
                'products' => $this->cart->getItems()
            ]
        );
        return $this->view;
    }

    public function applyCouponAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }
        return $this->view;
    }

    public function getBadgeCountAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function getSubtotalAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function getShippingAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function getTotalAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }
}
