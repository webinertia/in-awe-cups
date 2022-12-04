<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Listener;

use App\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Insert;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

use function implode;

final class CategoriesListener extends AbstractListenerAggregate
{
    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        //$this->listeners[] = $events->attach(EventFeature::EVENT_PRE_INSERT, [$this, 'preInsert'], $priority);
        //$this->listeners[] = $events->attach(EventFeature::EVENT_PRE_UPDATE, [$this, 'preUpdate'], $priority);
    }

    public function preInsert(TableGatewayEvent $event): void
    {

    }

    public function preUpdate(TableGatewayEvent $event): void
    {

    }
}
