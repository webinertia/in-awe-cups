<?php

declare(strict_types=1);

namespace App\View\Resolver;

use App\Model\Theme;
use Laminas\View\Exception;
use Laminas\View\Resolver\TemplatePathStack as Stack;

use function gettype;
use function is_string;
use function str_contains;

class TemplatePathStack extends Stack
{
    public function addPaths(array $paths)
    {
        $theme = new Theme();
        $activeTheme = $theme->getActiveTheme();
        foreach ($paths as $path) {
            if (! str_contains($path, 'laminas-developer-tools')) {
                $this->addPath($path . '/' . Theme::DEFAULT_THEME);
                if ($activeTheme !== Theme::DEFAULT_THEME) {
                    $this->addPath($path . '/' . $activeTheme);
                }
            } else {
                $this->addPath($path);
            }
        }
        return $this;
    }
}
