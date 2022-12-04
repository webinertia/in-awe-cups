<?php

declare(strict_types=1);

namespace Widget\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Widget\Model\ImageSlider;

class ImageSliderModelFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ImageSlider
    {
        return new $requestedName($container->get('config')['module_settings']['widget']['imageslider']);
    }
}
