<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use Dojo\Data;
use Laminas\Form\FormElementManager;
use Laminas\Http\Header\ContentRange;
use App\Upload\UploadEvent;
use Laminas\View\Model\JsonModel;
use Store\Api\Form\APiProductForm;
use Store\Model\Image;
use Store\Model\Product;

use function array_merge_recursive;

final class ProductsApiController extends AbstractApiController
{
    /** @var Data $dojoData */
    protected $dojoData;
    /** @var FormElementManager $formManager */
    protected $formManager;
    /** @var Image $image */
    protected $image;
    /** @var JsonModel $view */
    protected $view;
    /** @var Product $product */
    protected $product;
    /** @var ProductOptions $productOptions */
    protected $productOptions;

    public function __construct(FormElementManager $formManager, Product $product, Image $image, array $config)
    {
        parent::__construct($config);
        $this->setIdentifierName('id');
        $this->formManager = $formManager;
        $this->image       = $image;
        $this->product     = $product;
        $this->dojoData    = new Data($this->getIdentifierName(), null, 'label');
    }

    public function get($id)
    {
        if ($id !== null) {
            // return the object
            $this->dojoData->setItem($this->product->fetchByColumn('id', $id)->toArray());
            return $this->dojoData->toArray();
        } else {
            return $this->getList();
        }
    }

    public function update($id, $data)
    {
        $this->product->exchangeArray($data);
        try {
            if ($this->product->save()) {
                $this->response->setStatusCode(202);
            }
            return new JsonModel($this->dojoData->setItem($this->product->fetchByColumn('id', $id))->toArray());
        } catch (\Throwable $th) {
            $this->response->setStatusCode(500);
        }
    }

    // public function create($data)
    // {
    //     $this->product->exchangeArray($data);
    //     $files = $this->request->getFiles()->toArray();
    //     try {
    //         $result = $this->product->save($this->product);
    //         $this->image->productId = $id = $this->product->getLastInsertId();
    //         //$this->getEventManager()->trigger(UploadEvent::EVENT_UPLOAD, $this->image, )
    //         $this->response->setStatusCode(202);
    //         return new JsonModel($this->dojoData->setItem($this->product->fetchByColumn('id', $id))->toArray());
    //     } catch (\Throwable $th) {
    //         $this->response->setStatusCode(500);
    //     }
    // }

    public function delete($id)
    {
        // delete resource
        // set response code $this->response->setStatusCode($int);
        if ($this->product->delete((int) $id)) {
            $this->getEventManager()->trigger(UploadEvent::EVENT_DELETE, $this->image, ['productId' => $id]);
        }
        //$result = true;
        if ($result) {
            $this->response->setStatusCode(204);
            return new JsonModel();
        } else {
            $this->response->setStatusCode(500);
            return new JsonModel();
        }
    }

    public function getList()
    {
        $this->dojoData->setItems($this->product->fetchAll(true));
        $headers = $this->response->getHeaders();
        // todo: verify how the store expects this to be formatted within the data returned
        $headers->addHeaderLine('Content-Range', '0-1/1');

        return new JsonModel($this->dojoData->toArray());
    }

    public function deleteList($data)
    {
        // delete resource
        // set response code
    }
}