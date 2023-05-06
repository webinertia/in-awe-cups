<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ImageTable;
use Store\Model\Image;
use User\Service\UserServiceInterface;

final class ImageFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Image
    {
        $filterManager = $container->get('FilterManager');
        $renameUpload  = $filterManager->get(RenameUpload::class);
        $renameUpload->setOptions(
            $container->get('config')['module_settings']['store']['upload']['renameUploadConfig']
        );
        return new $requestedName(
            $container->get(UserServiceInterface::class),
            $renameUpload,
            $filterManager->get(BaseName::class),
            $container->get(ImageTable::class),
            $container->get('config'),
            $container->get(AdapterPluginManager::class)
        );
    }
}
