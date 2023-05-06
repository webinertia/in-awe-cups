<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\ModelInterface;
use App\Stdlib\ArrayUtils;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Store\Exception;
use Store\Model\OptionsPerProduct;
use Store\Model\Order;
use Store\Model\Product;

use const PHP_ROUND_HALF_UP;

use function round;
use function serialize;
use function sha1;

class Cart implements ModelInterface
{
    /** temp shipping per pound */
    public const SHIP_COST = 2.65;
    /** @var Container $container */
    protected $container;
    /** @var Acl $acl */
    protected $acl;
    /** @var AuthenticationService $auth */
    protected $auth;
    /** @var UserService $user */
    protected $user;
    /** @var OptionsPerProduct $optionLookup */
    protected $optionLookup;
    /** @var Order $order */
    protected $order;
    /** @var Product $product */
    protected $product;
    /** @var int $itemCount */
    protected $itemCount;
    protected float $total;

    /**
     *
     * @param ContainerInterface $container
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        Container $container,
        OptionsPerProduct $optionLookup,
        Order $order,
        Product $product
    ) {
        $this->optionLookup = $optionLookup;
        $this->order        = $order;
        $this->product      = $product;
        $this->container    = $container;
    }

    protected function addItem(array $item): void
    {
        $item['cartId'] = sha1(serialize($item));
        if ($this->container->offsetExists('items')) {
            $items = $this->container->offsetGet('items');
        } else {
            $items = [];
        }
        $items[$item['id']][] = $item;
        $this->container->offsetSet('items', $items);
    }

    public function addToCart(array $item): void
    {
        if (! isset($item['id']) || ! isset($item['quantity'])) {
            throw new Exception\DomainException('Item can not be added to the cart.');
        }
        if (isset($item['quantity'])) {
            $itemCount = $item['quantity'];
            unset($item['quantity']);
            for ($i=0; $i < $itemCount; $i++) {
                $this->addItem($item);
            }
        }
    }

    public function removeItem($id, $cartId): bool
    {
        $removed = false;
        if (! $this->container->offsetExists('items')) {
            throw new Exception\DomainException('Cart is empty.');
        }
        $items = $this->container->offsetGet('items');
        if (isset($items[$id])) {
            $count = count($items[$id]);
            // if there is only a single item in the cart with this id then just remove
            // it as it has to be the target. Prevents $items[$id][]
            if ($count === 1) {
                unset($items[$id]);
                $removed = true;
            } else {
                for ($i=0; $i < $count; $i++) {
                    if (isset($items[$id][$i]['cartId']) && $items[$id][$i]['cartId'] === $cartId) {
                        unset($items[$id][$i]);
                        $removed = true;
                        break;
                    }
                }
            }
        }
        $this->container->offsetSet('items', $items);
        return $removed;
    }

    public function getSubTotal(): float
    {
        $items = $this->container->offsetGet('items');
        $subTotal = 0.00;
        if (null !== $items && count($items) > 0) {
            foreach ($items as $group) {
                foreach ($group as $item) {
                    if (isset($item['cost'])) {
                        $cost = (float) $item['cost'];
                        $subTotal = $subTotal + $cost;
                    }
                }
            }
        }
        return $subTotal;
    }

    public function getTotal(): float
    {
        return $this->getSubTotal() + $this->getShippingCost();
    }

    public function getItems()
    {
        return $this->container->offsetGet('items');
    }

    public function getItemCount(): int
    {
        if (! $this->container->offsetExists('items')) {
            $this->setItemCount(0);
            return $this->itemCount;
        }
        // stopped work here
        $items     = $this->container->offsetGet('items');
        $itemCount = 0;
        foreach ($items as $id => $itemGroup) {
            $itemCount = $itemCount + count($itemGroup);
        }
        $this->setItemCount($itemCount);
        return $this->itemCount;
    }

    public function getShippingCost(): float
    {
        // method will be replaced
        $items = $this->container->offsetGet('items');
        $shipping = 0.00;
        if (null !== $items && count($items) > 0) {
            foreach ($items as $group) {
                foreach ($group as $item) {
                    if (isset($item['weight'])) {
                        // if ($shipping !== 0.00) {
                        //     $shipping = round($shipping, 2, PHP_ROUND_HALF_UP);
                        // }
                        $shipping = $shipping + self::SHIP_COST * $item['weight'];
                    }
                }
            }
        }
        $shipping = round($shipping, 2, PHP_ROUND_HALF_UP);
        return $shipping;
    }

    protected function setItemCount(int $itemCount): void
    {
        $this->itemCount = $itemCount;
    }

    public function getResourceId(): ResourceInterface|string
    {
        return 'Cart';
    }

    public function emptyCart(): void
    {
        $this->container->offsetUnset('items');
    }

    public function getOwnerId(): mixed
    {
        return $this->container->userId;
    }
}
