<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Filter\LabelToTitle;
use App\Filter\TitleToLabel;
use App\Upload\UploadEvent;
use Laminas\Filter\FilterPluginManager;
use Laminas\Form\FormElementManager;
use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Model\Category;
use Store\Model\Image;
use Store\Model\Product;
use Store\Form\ProductForm;

use function array_merge_recursive;

class AdminProductsController extends AbstractAppController implements AdminControllerInterface
{
    /** @var Category $category */
    protected $category;
    /** @var Image $image */
    protected $image;
    /** @var LabelToTitle $labelToTitleFilter */
    protected $labelToTitleFilter;
    /** @var Product $product */
    protected $product;
    /** @var ProductForm $form */
    protected ProductForm $form;
    /** @var string $resourceId */
    protected $resourceId = 'admin';

    /** @return void */
    public function __construct(
        Category $category,
        Image $image,
        Product $product,
        FilterPluginManager $filterPluginManager,
        FormElementManager $formElementManager,
        array $config,
    ) {
        parent::__construct($config);
        $this->category           = $category;
        $this->image              = $image;
        $this->product            = $product;
        $this->labelToTitleFilter = $filterPluginManager->get(TitleToLabel::class);
        $this->form               = $formElementManager->get(ProductForm::class);
    }

    public function indexAction(): ModelInterface
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        return $this->view;
    }

    public function manageSettings()
    {
    }

    public function managerAction(): ModelInterface
    {
        // Refactor this into individual action since were using ajax
        // This action is just the central endpoint for accessing all subactions
        // This is moving to the indexAction for simplification
        $sessionContainer = new Container();
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $data = [];

        $data['product-info']['userId'] = $this->userService->id;

        $product = $this->getService(Product::class);
        $id      = $this->params('id', null);
        /**
         * Even though we will not be creating the product here, we need to
         * setup the form and populate it with its default values
         * along with setting the correct action attribute so that it
         * will post to the correct action when submitted
         */
        $product->id               = $id;
        $product->name             = '';
        $product->userId           = $this->userService->id;
        $product->categoryId       = null;
        $product                   = $product->save($product);
        $sessionContainer->product = $product->toArray();

        $data['product-info']['id'] = $product->id;

        $this->form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'admin.store/products/manager',
                ['action' => 'edit', 'id' => $product->id]
            )
        );
        $this->form->setData(array_merge_recursive($this->uploadConfig, $data));
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }

    public function createAction()
    {
        $this->ajaxAction();
        $this->form->setAttribute(
            'action',
            '/admin/store/manage/products/create'
        );
        if ($this->request->isPost()) {
            $posted = array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            );
            $this->form->setData(array_merge_recursive(
                    $this->request->getPost()->toArray(),
                    $this->request->getFiles()->toArray()
                )
            );
            if ($this->form->isValid()) { // if form data is valid proceed
                $data = $this->form->getData();
                try {
                    $this->product->exchangeArray($data['product-data']);
                    $this->product->title = $this->labelToTitleFilter->filter($data['product-data']['label']);
                    $this->product->save($this->product);
                    $this->image->productId = $this->product->getLastInsertId();
                    $this->image->setUploadType(Image::PRODUCT_TYPE);
                    $this->getEventManager()->trigger(UploadEvent::EVENT_UPLOAD, $this->image, $data['image-data']['images']);
                    return new JsonModel([
                        'success' => true,
                        'message' => $this->product->label . ' was created successfully!',
                    ]);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            } else {
                $messages = $this->form->getMessages();
            }
        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }

    public function editAction(): ModelInterface
    {
        $id            = $this->params('id');
        $this->product = $this->product->fetchByColumn('id', $id);
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->request->isPost()) {
            $this->form->setAttribute(
                'action',
                $this->url()->fromRoute(
                    'admin.store/product/manager',
                    ['action' => 'edit', 'id' => $id]
                )
            );
            $this->form->setdata(array_merge_recursive($this->uploadConfig, $this->product->toArray()));
        }
        return $this->view;
    }

    public function uploadImagesAction()
    {
    }

    public function deleteAction()
    {
    }
}
