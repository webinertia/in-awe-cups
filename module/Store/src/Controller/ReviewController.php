<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;

final class ReviewController extends AbstractAppController
{
    public function indexAction()
    {

    }

    public function createAction()
    {

    }

    public function listAction()
    {
        $productId = $this->params()->fromRoute('productId', '0');
    }
}
