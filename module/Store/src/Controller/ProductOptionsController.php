<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Controller\AdminControllerInterface;
use Dojo\Data as DojoData;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\ProductOptions;
/**
 * This class provides the store admin area with the option store API handling etc
 * @package Store\Controller
 */
final class ProductOptionsController extends AbstractApiController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'store';
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var DojoData $dojoData */
    protected $dojoData;

    public function __construct(ProductOptions $productOptions, array $config)
    {
        parent::__construct($config);
        $this->setIdentifierName('id');
        $this->productOptions = $productOptions;
        $this->dojoData       = new DojoData($this->getIdentifierName(), null, 'option');
    }

    public function get($id)
    {
        return new JsonModel(['id' => $id]);
    }

    // (Manage) Categories | Products | Options ui
    public function getList()
    {
        $this->dojoData->setItems($this->productOptions->fetchGrid());
        $this->response->getHeaders()->addHeaderLine('Content-Range', '0-1/1');
        return new JsonModel($this->dojoData->toArray());
    }

    public function update($id, $data)
    {

    }

    public function delete($id)
    {

    }
}
