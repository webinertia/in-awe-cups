<?php

declare(strict_types=1);

namespace Payment\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Payment\Service\Gateway;
use Ramsey\Uuid;
use Store\Model\Cart;

final class GatewayController extends AbstractAppController
{
    protected Cart $cart;
    protected Gateway $gateway;
    /** @var string $customerId */
    protected $customerId;

    public function __construct(Cart $cart, Gateway $gateway, array $config)
    {
        $this->cart    = $cart;
        $this->gateway = $gateway;
        parent::__construct($config);
    }

    public function createAction(): ModelInterface
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
        }
        return $this->view;
    }

    public function confirmationAction(): ModelInterface
    {
        if ($this->request->isPost()) {
            $transData = [];
            $transData['options']['submitForSettlement'] = true;
            $transData['amount'] = (string) $this->cart->getTotal();
            $filter = ['paymentMethodNonce' => '', 'deviceData' => ''];
            $post                = $this->request->getPost()->toArray();
            $transaction         = $post += $transData;
            $result              = $this->gateway->transaction()->sale($transaction);
            if ($result->success || $result->transaction !== null) {
                // save to order table with transaction id and other needed data
                // clear cart
                // $this->redirect()->toRoute('/store/cart/transaction-details', [], ['query' => ['id' => $result->transaction->id]]);
                $this->view->setVariable('transaction', $result->transaction);
            }
        } else {

        }
        return $this->view;
    }
}
