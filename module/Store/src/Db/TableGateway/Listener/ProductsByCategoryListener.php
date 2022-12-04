<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Listener;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

class ProductsByCategoryListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {

    }
}
