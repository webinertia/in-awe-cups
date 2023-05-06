<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 * phpcs:ignoreFile
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\Trait\JsonDataTrait;
use App\Log\LoggerAwareInterface;
use Laminas\View\Model\ViewModel;
use User\Acl\ResourceAwareTrait;
use App\Upload\UploadEvent;
use App\Upload\UploadAwareInterface;
use Store\Model\Category;
use Store\Model\Image;
use Laminas\Form\FormElementManager;
use Store\Form\CategoryForm;
use Store\Form\DojoTest;
use Braintree\Gateway as PaymentGateway;
use Payment\Form\Shipping;

use function strpos;
use function str_replace;

final class TestController extends AbstractAppController implements LoggerAwareInterface, UploadAwareInterface
{
    use JsonDataTrait;
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'test';
    protected $formManager;
    protected PaymentGateway $gateway;

    public function __construct(
        FormElementManager $formManager,
        array $config
    ) {
        parent::__construct($config);
        $this->formManager = $formManager;
    }

    public function indexAction(): ViewModel
    {
        $this->view->setVariable('form', $this->formManager->get(Shipping::class));
        return $this->view;
    }
}
