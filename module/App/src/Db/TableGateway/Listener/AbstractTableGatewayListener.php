<?php

declare(strict_types=1);

namespace App\Db\TableGateway\Listener;

use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

abstract class AbstractTableGatewayListener extends AbstractListenerAggregate
{

}
