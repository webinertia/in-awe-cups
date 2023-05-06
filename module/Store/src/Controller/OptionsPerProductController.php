<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use App\Form\FormManagerAwareInterface;
use App\Form\FormManagerAwareTrait;
use Dojo\Data as DojoData;
use Laminas\Stdlib\ArrayUtils;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\ModelInterface;
use Store\Form\ProductOptions as OptionsForm;
use Store\Model\OptionsPerProduct;
use Store\Model\Product;
use Store\Model\ProductOptions;

class OptionsPerProductController extends AbstractApiController implements FormManagerAwareInterface
{
    use FormManagerAwareTrait;

    /** @var DojoData $dojoData */
    protected $dojoData;
    /** @var OptionsPerProduct $optionLookup */
    protected $optionLookup;
    /** @var Product $product */
    protected $product;
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var string $resourceId */
    protected $resourceId = 'store';
    /** @var string $privilege */
    private $privilege = 'edit-product';

    public function __construct(
        OptionsPerProduct $optionLookup,
        Product $product,
        ProductOptions $productOptions,
        array $config
    ) {
        parent::__construct($config);
        $this->optionLookup   = $optionLookup;
        $this->product        = $product;
        $this->productOptions = $productOptions;
    }

    public function get($id)
    {
        if (! $this->isAllowed($this->userService, $this, $this->privilege)) {
            $this->response->setStatusCode(403);
        }
        $params = $this->params()->fromRoute();
        $product = $this->product->fetchByColumn('id', $params['id']);
        $optionForm = $this->getFormManager()->build(
            OptionsForm::class,
            [
                'productId'   => $params['id'],
                'category'    => $product->category,
                'optionGroup' => $params['optionGroup'],
            ]
        );
        $optionForm->setData([
            'productId'   => $params['id'],
            'category'    => $product->category,
            'optionGroup' => $params['optionGroup'],
        ]);

        $currentOptions = $this->optionLookup->fetchByOptionGroup($params['id'], $params['optionGroup']);

        //$result = $this->productOptions->fetch
        $view = new ViewModel(['form' => $optionForm]);
        $view->setTerminal(true);
        return $view;
    }

    public function getList()
    {

    }

    public function update($id, $data)
    {
        $params = $this->params()->fromRoute();
        if (ArrayUtils::isList($data['productOptions'])) {
            $this->optionLookup->save($data);
        }
        return new JsonModel([]);
    }
}
