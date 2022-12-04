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

use function strpos;
use function str_replace;

final class TestController extends AbstractAppController implements LoggerAwareInterface, UploadAwareInterface
{
    use JsonDataTrait;
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'test';
    protected $formManager;

    public function __construct(
        Category $category,
        FormElementManager $formElementManager,
        Image $imageModel,
        array $config)
    {
        parent::__construct($config);
        $this->category = $category;
        $this->formManager = $formElementManager;
        $this->imageModel  = $imageModel;
        $this->form        = $this->formManager->get(DojoTest::class);
    }

    public function indexAction(): ViewModel
    {
        $this->ajaxAction();
        $this->form->setAttribute('action', $this->url()->fromRoute('admin.store/manage/categories', ['action' => 'create']));
        // $data = [
        //     'label' => 'test category data    ',
        //     //'description' => 'test description',
        // ];
        // $this->category->exchangeArray($data);
        // $this->category->save($this->category);
        $this->view->setVariable('form', $this->form);

        return $this->view;
    }
}
