<?php

declare(strict_types=1);

namespace App\Upload;

use Laminas\EventManager\Event;

class UploadEvent extends Event
{
    public const EVENT_UPLOAD = 'upload';
    public const EVENT_DELETE = 'delete';
}
