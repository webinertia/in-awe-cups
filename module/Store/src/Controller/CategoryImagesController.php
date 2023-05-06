<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Controller\AdminControllerInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Store\Model\Image;

final class CategoryImagesController extends AbstractApiController implements AdminControllerInterface
{
    /** @var Image $image */
    protected $image;
    /** @var string $resourceId */
    protected $resourceId = 'store';

    public function __construct(Image $image, array $config)
    {
        parent::__construct($config);
        $this->image  = $image;
    }

    public function get($id): ModelInterface
    {
        $this->ajaxAction();
        $this->view->setVariable('model', $this->image->fetchByColumn('categoryId', $id, true));
        return $this->view;
    }
}
