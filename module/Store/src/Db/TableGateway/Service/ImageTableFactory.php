<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ImageTable;
use Store\Model\Image;
use User\Service\UserServiceInterface;

final class ImageTableFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ImageTable
    {
        $filterManager = $container->get('FilterManager');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(
            new Image(
                $container->get(UserServiceInterface::class),
                $filterManager->get(RenameUpload::class),
                $filterManager->get(BaseName::class)
            )
        );
        return new $requestedName(
            $container->get('config')['db']['store_image_table_name'],
            $container->get('EventManager'),
            $resultSetPrototype,
            false,
            null,
            $container->get(AdapterInterface::class)
        );
    }
}
