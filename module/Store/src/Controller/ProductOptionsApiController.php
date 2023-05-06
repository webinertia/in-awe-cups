<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Dojo\Data as DojoData;
use Laminas\View\Model\JsonModel;
use Store\Model\ProductOptions;

final class ProductOptionsApiController extends AbstractAppController
{
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var DojoData $dojoData */
    protected $dojoData;

    public function __construct(ProductOptions $productOptions, array $config)
    {
        parent::__construct($config);
        //$this->dojoData       = new Dojodata('productGroup', null, 'optionName');
        $this->productOptions = $productOptions;
    }

    public function optionsAction()
    {
        $this->ajaxAction();
       // $this->dojoData->setItems();
        return new JsonModel($this->productOptions->fetchOptionGroups(true));
    }
}
