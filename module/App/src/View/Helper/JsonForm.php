<?php

declare(strict_types=1);

namespace App\View\Helper;

use Laminas\Form\FormInterface;
use Laminas\Json\Json;
use Laminas\View\Helper\AbstractHelper;

class JsonForm extends AbstractHelper
{
     public function __invoke(?FormInterface $form = null)
     {
        $this->formHelper = $this->getView()->bootstrapForm();
        if (! $form) {
            return $this;
        }
        return Json::encode($this->formHelper->render($form));
     }
}
