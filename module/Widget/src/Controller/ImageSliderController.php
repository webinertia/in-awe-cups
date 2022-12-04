<?php

declare(strict_types=1);

namespace Widget\Controller;

use App\Controller\AbstractAppController;
use Widget\Model\ImageSlider;

class ImageSliderController extends AbstractAppController
{
    public function indexAction()
    {
        $sliderSettings = $this->config['module_settings']['widget']['imageslider'];
        $model          = $this->getService(ImageSlider::class);
        $this->view->setVariable('sliderData', $model);
        return $this->view;
    }
}
