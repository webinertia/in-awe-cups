<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Controller\AdminControllerInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Store\Model\Image;
use User\Acl\AclAwareTrait;

final class ProductImagesController extends AbstractApiController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'store';
    /** @var Image $image */
    protected $image;

    /** @return void */
    public function __construct(Image $image, array $config)
    {
        parent::__construct($config);
        $this->image  = $image;
    }

    public function get($id): ModelInterface
    {
        $this->ajaxAction();
        $this->view->setVariable('model', $this->image->fetchByColumn('productId', $id, true));
        return $this->view;
    }
}
