<?php

declare(strict_types=1);

namespace ContentManager\Navigation;

use ContentManager\Navigation\NavigationInterface;
use Laminas\Navigation\Exception\InvalidArgumentException;
use Traversable;

use function is_array;

/**
 * A simple container class for {@link Laminas\Navigation\Page} pages
 */
class Navigation extends AbstractContainer implements NavigationInterface
{
    /**
     * Creates a new navigation container
     *
     * @param  array|Traversable $pages [optional] pages to add
     */
    public function __construct($pages = null)
    {
        if ($pages && (! is_array($pages) && ! $pages instanceof Traversable)) {
            throw new InvalidArgumentException(
                'Invalid argument: $pages must be an array, an '
                . 'instance of Traversable, or null'
            );
        }

        if ($pages) {
            $this->addPages($pages);
        }
    }
}
