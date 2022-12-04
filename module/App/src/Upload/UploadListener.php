<?php

declare(strict_types=1);

namespace App\Upload;

use App\Upload\UploadEvent;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\AbstractListenerAggregate;

class UploadListener extends AbstractListenerAggregate
{
    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedManager     = $events->getSharedManager();
        $this->listeners[] = $sharedManager->attach(
            UploadAwareInterface::class,
            UploadEvent::EVENT_UPLOAD,
            [$this, 'upload'],
            $priority
        );
    }

    public function upload(EventInterface $event): void
    {
        $name   = $event->getName();
        $target = $event->getTarget();
        $params = $event->getParams();
        $target->handleUpload($params);
    }
}
