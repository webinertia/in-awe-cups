<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\ModelInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Session\Container;
use Store\Model\Order;
use Store\Model\Product;

class Cart implements ModelInterface
{
    /** @var Container $session */
    private $session;
    /**  @var Acl $acl */
    protected $acl;
    /** @var AuthenticationService $auth */
    protected $auth;
    /** @var UserService $user */
    protected $user;
    /** @var Order $order */
    protected $order;
    /** @var Product $product */
    protected $product;
    /** @var int $itemCount */
    protected $itemCount;
    protected float $total;
    /**
     * Array of Product instances
     *
     * @var array<int, Product> $items
     */
    protected $items = [];

    protected array $data = [];
    /**
     *
     * @param ContainerInterface $container
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(Container $session)
    {
       // $this->acl = $container->get('Acl');
       // $this->auth = $container->get(AuthenticationService::class);
       // $this->product = $container->get(Product::class);
       // $this->order = $container->get(Order::class);
       // $this->userTable = $container->get(UserTable::class);
        $this->session = $session;
    }

    public function setItems(array $items)
    {
        $this->session->items = $items;
    }

    public function getResourceId(): ResourceInterface|string
    {
        return 'Cart';
    }

    public function getOwnerId(): mixed
    {
        return $this->session->userId;
    }
}
