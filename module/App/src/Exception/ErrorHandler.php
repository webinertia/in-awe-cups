<?php

declare(strict_types=1);

namespace App\Exception;

use App\Log\LoggerAwareInterface;
use App\Log\LoggerAwareTrait;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Stdlib\ErrorHandler as BaseClass;

class ErrorHandler extends BaseClass implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function setEventManager(EventManagerInterface $eventManager)
    {

    }

    public function getEventManager()
    {
        return $this->eventManager;
    }
}
