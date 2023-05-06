<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Controller\AdminControllerInterface;
use App\Log\LogEvent;
use App\Upload\UploadEvent;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\JsonModel;
use Store\Form\UploadForm;
use Store\Model\Category;
use Store\Model\Exception\ImageManagerException;
use Store\Model\Image;

final class CategoryImagesManager extends AbstractApiController implements AdminControllerInterface
{
    /** @var Image $image */
    protected $image;
    /** @var FormElementManager $formManager */
    protected $formManager;
    /** @var string $resourceId */
    protected $resourceId = 'store';

    public function __construct(Image $image, FormElementManager $formElementManager, array $config)
    {
        parent::__construct($config);
        $this->formManager = $formElementManager;
        $this->image       = $image;
    }

    public function get($id)
    {
        $this->ajaxAction();
        $form = $this->formManager->get(UploadForm::class);
        $data['file-data']['categoryId'] = $id;
        $form->setData($data);
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function create($data)
    {
        $form   = $this->formManager->get(UploadForm::class);
        $merged = array_merge_recursive(
            $data, $this->request->getFiles()->toArray()
        );
        $form->setData($merged);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                $this->getEventManager()->trigger(UploadEvent::EVENT_UPLOAD, $this->image, $data['file-data']);
                $this->response->setStatusCode(201);
                return new JsonModel([]);
            } catch (\Throwable $th) {
                $this->response->setStatusCode(500);
                return new JsonModel(['message' => $th->getMessage()]);
            }
        } else {
            $this->response->setStatusCode(406);
            return new JsonModel(['message' => $form->getMessages()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->getEventManager()->trigger(UploadEvent::EVENT_DELETE, $this->image, ['id' => $id]);
            $this->response->setStatusCode(204);
            return new JsonModel(['message' => 'Image was successfully deleted.']);
        } catch (ImageManagerException $ex) {
            $this->getEventManager()->trigger(LogEvent::ERROR, $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            $this->response->setStatusCode(500);
            return new JsonModel(['message' => $ex->getMessage()]);
        }
    }
}
