<?php

declare(strict_types=1);

namespace InAweCups\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function sprintf;

class PageHeader extends AbstractHelper
{
    public function __invoke(string $h1 = 'Page Title'): string
    {
        $html = '<div class="container-fluid bg-secondary mb-5">
                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
                        <h1 class="font-weight-semi-bold mb-3">%s</h1>
                    </div>
                </div>';
        return sprintf($html, $h1);
    }
}
