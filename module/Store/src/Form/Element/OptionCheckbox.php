<?php

declare(strict_types=1);

namespace Store\Form\Element;

use Laminas\Form\Element\MultiCheckbox;
use Store\Model\OptionsPerProduct;
use Store\Model\ProductOptions;

final class OptionCheckbox extends MultiCheckbox
{
    /** @var OptionsPerProduct $optionsPerProduct */
    protected $optionsPerProduct;
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var array<mixed> $initialValues */
    protected $initialValues = [];
    // protected $attributes = [
    //     //'data-dojo-type' => 'dijit/form/CheckBox',
    // ];
    /**
     * $options[
     * 'optionGroup' => $optionGroup,
     * 'id' => $productId,
     * ];
     * @param array<string, mixed> $options */

    public function __construct(OptionsPerProduct $optionsPerProduct, ProductOptions $productOptions, $name = 'productOptions', $options = [])
    {
        parent::__construct('productOptions', $options);
        $this->optionsPerProduct = $optionsPerProduct;
        $this->productOptions    = $productOptions;
    }

    public function init()
    {
        $options = $this->getOptions();
        $possibleOptions = $this->productOptions->fetchOptions($options['category'], $options['optionGroup']);
        $currentOptions  = $this->optionsPerProduct->fetchMultiCheckboxValues(
                                                                            $options['productId'],
                                                                            $options['category'],
                                                                            $options['optionGroup'],
                                                                            true
                                                                        );
        //$valuesOptionArray = $this->normalizeValueOptions($possibleOptions, $options['idAsValue']);
        $this->setInitialValues($currentOptions);
    }

    protected function normalizeValueOptions(array $data, bool $idAsValue = true): array
    {
        $values = [];
        foreach ($data as $row) {
            $values[$idAsValue ? $row['id'] : $row['option']] = $row['option'];
        }
        return $values;
    }

    protected function setInitialValues(array $values): void
    {
        $this->initialValues = $values;
    }

    public function getInitialValues(): array
    {
        return $this->initialValues;
    }
}
