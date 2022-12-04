<?php

declare(strict_types=1);

namespace InAweCups;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'view_helpers' => [
        'aliases'   => [
            'categoryMenu' => View\Helper\CategoryMenu::class,
            'pageHeader' => View\Helper\PageHeader::class,
        ],
        'factories' => [
            View\Helper\CategoryMenu::class => View\Helper\Factory\CategoryMenuFactory::class,
            View\Helper\PageHeader::class => InvokableFactory::class,
        ],
    ],
];
