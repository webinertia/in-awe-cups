<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Controller\AdminControllerInterface;

/**
 * This file is only reponsible for loading the view file for the admin area
 * and providing a template file to inititialize the javascript etc.
 * @package Store\Controller
 */
final class ProductOptionsManager extends AbstractApiController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'store';

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function getList()
    {
        $this->ajaxAction();
        return $this->view;
    }
}
