<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use Dojo\Data as DojoData;
use Laminas\Form\Exception\InvalidElementException;
use Laminas\Db\Exception\RuntimeException;
use Laminas\Form\FormElementManager;
use Laminas\Http\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Exception\CyclicAliasException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\View\Model\JsonModel;
use Store\Form\OptionGroupForm;
use Store\Model\Exception\OptionExistsException;
use Store\Model\ProductOptions;
use Store\Model\OptionsPerProduct;

class ProductOptionsGroupController extends AbstractApiController
{
    /** @var string $resourceId */
    protected $resourceId = 'store';
    /** @var OptionPerProduct $optionLookup */
    protected $optionLookup;
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var DojoData $dojoData */
    protected $dojoData;
    /** @var FormElementManager $formManager */
    protected $formManager;

    public function __construct(
        ProductOptions $productOptions,
        OptionsPerProduct $optionLookup,
        FormElementManager $formElementManager,
        array $config) {
        parent::__construct($config);
        $this->setIdentifierName('id');
        $this->formManager    = $formElementManager;
        $this->productOptions = $productOptions;
        $this->optionLookup   = $optionLookup;
    }

    public function get($id)
    {
        return new JsonModel(
            $this->productOptions->fetchByColumn(
                $this->getIdentifierName(),
                $id
            )
        );
    }

    /**
     * Used By the ContextMenu
     * get a list of all of the optionGroups
     **/
    public function getList()
    {
        return new JsonModel($this->productOptions->fetchOptionGroups(true));
    }

    /**
     * Creates and uses the following custom response codes
     * option codes are
     * 227 => 'Option Exists', prompts client to confirmEdit
     * 228 => 'Option Form Error(s)', prompts client to correct form fields, currently not supported
     * @param mixed $data
     * @return JsonModel
     * @throws InvalidArgumentException
     * @throws InvalidElementException
     * @throws ContainerModificationsNotAllowedException
     * @throws CyclicAliasException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws RuntimeException
     */
    public function create($data): JsonModel
    {
        $form = $this->formManager->get(OptionGroupForm::class);
        $form->setData($data);
        $form->setValidationGroup(['category', 'optionGroup', 'option', 'cost']);
        if ($form->isValid()) {
            $data = $form->getData();
            if ($this->productOptions->noRecordExists($data)) {
                if ($this->productOptions->save($data)) {
                    $this->response->setStatusCode(201);
                    return new JsonModel([['message' => $data['category'] . ' Option ' . $data['optionGroup'] . ' ' . $data['option'] . ' was created.']]);
                } else {
                    $this->response->setStatusCode(513)->setReasonPhrase('Data not saved');
                    return new JsonModel(['message' => $this->response->getReasonPhrase()]);
                }
            } else {
                $this->response->setStatusCode(409);
                return new JsonModel(['message' => 'Option Already Exist']);
            }
        }
    }

    // handle edits, via put method
    public function update($id, $data): JsonModel
    {
        $form = $this->formManager->get(OptionGroupForm::class);
        $form->setData($data);
        $form->setValidationGroup(['id', 'category', 'optionGroup', 'option', 'cost']);
        if ($form->isValid()) {
            $data = $form->getData();
            // we only send the id as an array because the validation method will add a where for every column that is present
            // since we have the primary key, its all we need ;)
            if ($this->productOptions->recordExists(['id' => $data['id']])) {
                if ($this->productOptions->save($data) && $this->optionLookup->update($data)) {
                    $this->response->setStatusCode(202);
                    return new JsonModel(['message' => $data['category'] . ' Option ' . $data['optionGroup'] . ' ' . $data['option'] . ' was updated.']);
                } else {
                    $this->response->setStatusCode(513)->setReasonPhrase('Data not saved');
                    return new JsonModel(['message' => $this->response->getReasonPhrase()]);
                }
            } else {
                // we can only edit this if its found, if not then let them know it
                $this->response->setStatusCode(455)->setReasonPhrase('Unknown Resource Requested');
                return new JsonModel(['message' => 'Option Not Found']);
            }
        }
    }
}
