<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use Dojo\Data;
use Laminas\View\Model\JsonModel;
use Store\Model\Category;

final class CategoriesApiController extends AbstractApiController
{
    /** @var Category $category */
    protected $category;
    /** @var array<mixed> $config */
    protected $config;
    /** @var Data $dojoData */
    protected $dojoData;
    /** @return void */
    public function __construct(Category $category, array $config)
    {
        parent::__construct($config);
        $this->setIdentifierName('id');
        $this->category = $category;
        $this->config   = $config;
        $this->view     = new JsonModel();
        $this->dojoData = new Data($this->getIdentifierName(), null, 'label');
    }

    public function getList(): JsonModel
    {
        //$this->dojoData->setItems($this->category->fetchAll(true));
        $this->dojoData->setItems($this->category->fetchGridsStore(false));
        $headers = $this->response->getHeaders();
        // todo: verify how the store expects this to be formatted within the data returned
        $headers->addHeaderLine('Content-Range', '0-1/1');

        return new JsonModel($this->dojoData->toArray());
    }

    public function update($id, $data): JsonModel
    {
        try {
            $identifier = $this->getIdentifierName();
            $data[$identifier] = $id;
            $this->category->save($data, [$identifier => $id]);
            $this->response->setStatusCode(202);
            return new JsonModel($this->dojoData->setItem($this->category->fetchByColumn('id', $id)->toArray())->toArray());
        } catch (\Throwable $th) {
            $this->response->setStatusCode(500);
            return new JsonModel(['message' => $th->getMessage()]);
        }

    }

    public function delete($id)
    {
        try {
            $result = $this->category->delete([$this->getIdentifierName() => $id]);
            if ($result) {
                $this->response->setStatusCode(204);
            } else {
                $this->response->setStatusCode(500);
            }
        } catch (\Throwable $th) {
            $this->response->setStatusCode(500);
        }
    }
}
