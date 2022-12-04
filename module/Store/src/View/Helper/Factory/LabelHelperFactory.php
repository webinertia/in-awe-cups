<?php

declare(strict_types=1);

namespace Store\View\Helper\Factory;

use App\Filter\TitleToLabel;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\View\Helper\LabelHelper;

final class LabelHelperFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LabelHelper
    {
        return new $requestedName($container->get('FilterManager')->get(TitleToLabel::class));
    }
}
