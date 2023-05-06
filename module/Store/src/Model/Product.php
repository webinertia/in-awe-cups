<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\AbstractModel;
use App\Filter\LabelToTitle;
use App\Filter\TitleToLabel;
use App\Log\LogEvent;
use App\Model\ModelTrait;
use App\Stdlib\ArrayUtils;
use App\Upload\UploadEvent;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Filter\FilterPluginManager;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\Paginator\Paginator;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Db\TableGateway\ProductsTable;
use Store\Model\Image;
use Store\Model\OptionsPerProduct;
use Store\Model\ProductOptions;

use function array_intersect_key;

class Product extends AbstractModel
{
    use ModelTrait;

    public const FIELDSET = 'product-data';

    /** @var AdapterPluginManager $adapterManager */
    protected $adapterManager;
    /** @var FilterPluginManager $filterManager*/
    protected $filterManager;
    /** @var Image $image*/
    protected $image;
    /** @var array<mixed> $fileData */
    protected $fileData;
    /** @var ProductsByCategoryTable $productsByCategoryTable */
    protected $productsByCategoryTable;
    /** @var OptionsPerProduct $optionsLookup*/
    protected $optionsLookup;
    /** @var Paginator $paginator */
    protected $paginator;
    /** @var bool $paginated */
    protected $paginated;
    /** @var int|string $itemCountPerPage */
    protected $itemCountPerPage;

    // string table names for use within queries
    /** @var string $c */
    private $c;
    /** @var string $i */
    private $i;
    /** @var string $t */
    private $t;

    public function __construct(
        ?ProductsTable $gateway = null,
        ?ProductsByCategoryTable $productsByCategoryTable = null,
        ?OptionsPerProduct $optionsLookup = null,
        ?Image $image = null,
        ?array $config = null,
        ?AdapterPluginManager $adapterManager = null,
        ?FilterPluginManager $filterManager = null
        ) {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
            $this->t = $this->gateway->getTable();
            $this->c = $config['db']['store_categories_table_name'];
            $this->i = $config['db']['store_image_table_name'];
        }
        if ($productsByCategoryTable !== null) {
            $this->productsByCategoryTable = $productsByCategoryTable;
        }
        if ($optionsLookup !== null) {
            $this->optionsLookup = $optionsLookup;
        }
        if ($image !== null) {
            $this->image = $image;
        }
        if ($adapterManager !== null) {
            $this->adapterManager = $adapterManager;
        }
        if ($filterManager !== null) {
            $this->filterManager = $filterManager;
        }
    }

    public function fetchDetail(string $title, ?bool $fetchArray = false): self
    {
        $where  = new Where();
        $select = new Select();
        $select->from(['p' => $this->t]);
        $where->equalTo('title', $title);
        $select->columns(['*']);
        $select->where($where);
        return $this->gateway->selectWith($select)->current();
    }

    public function fetchProductOptions(int $id, ?bool $fetchArray = false): ResultSet|array
    {
        $where = new Where();
        $select = new Select();
        $select->from('store_options_per_product');
        $where->equalTo('productId', $id);
        $result = $this->optionsLookup->getGateway()->selectWith($select);
        if ($fetchArray) {
            return $result->toArray();
        }
        return $result;
    }

    public function fetchAllByCategory(string $category, bool $fetchArray = true): ResultSetInterface|array
    {
        $where  = new Where();
        $select = $this->image->getGateway()->getSql()->select();
        $where->equalTo('category', $this->t . '.category');
        $select->join(
            $this->t,
            $this->t . '.id = ' . $this->i . '.productId',
            ['id', 'cost', 'label', 'title'],
            Select::JOIN_LEFT_OUTER,
        );
        $select->columns(['fileName']);
        $select->where($where);
        $result = $this->image->getGateway()->selectWith($select)->toArray();
        return $result;
    }

    public function fetchAllFiltered(): ResultSetInterface|array
    {
        $where  = new Where();
        $select = $this->gateway->getSql()->select();

        return $result;
    }

    public function fetchOptions(int|string $id, string $optionGroup = null)
    {
        $select = $this->optionsLookup->getGateway()->getSql()->select();
        $select->where(['productId' => $id]);
        if ($optionGroup !== null) {
            $select->where(['optionGroup' => $optionGroup]);
        }
        return $this->optionsLookup->getGateway()->selectWith($select);
    }

    public function fetchWithOptions(int|string $id)
    {
        $select = $this->gateway->getSql()->select();
        $select->join(
            ['o' => 'store_options_per_product'],
            'store_products.id = o.productId',
            ['optionGroup', 'optionValue'],
            Select::JOIN_INNER,
        );
        //$result = $this->gateway->selectWith($select);
        return $this->gateway->selectWith($select);
    }

    public function fetchMaxCost(?string $category = 'all', ?bool $roundUp = false, ?int $roundToDigit = 1): int|float
    {
        $where  = new Where();

        $select = $this->gateway->getSql()->select();
        $select->columns(
            [
                'category',
                'maxPrice' => 'cost',
            ]
        );
        if ($category !== 'all' && $category !== null) {
            $where->equalTo('category', $category);
        }
        $select->order(['maxPrice DESC'])->limit(1);
        $select->where($where);
        $max = (float) $this->gateway->selectWith($select)->current()->maxPrice;
        if ($roundUp) {
            return $this->roundToGivenDigit($max, $roundToDigit);
        }
        return $max;
    }

    public function save()
    {
        $data = $this->getArrayCopy();
        try {
            // decide if this is insert or update
            if($data['id'] !== null) {
                $this->gateway->update($data, ['id' => $data['id']]);
                return $data['id'];
            }
            // from here we should be inserting a new row
            $labelToTitleFilter = $this->filterManager->get(LabelToTitle::class);
            // normalize a title from the label
            $data['title'] = $labelToTitleFilter->filter($data['label']);
            // insert the row
            $this->gateway->insert($data);
            // get the created id
            return $this->gateway->getLastInsertValue();
        } catch (\Throwable $th) {
            $this->getEventManager()->trigger(LogEvent::NOTICE, $th->getMessage());
            throw $th;
        }

    }

    public function delete(int $id): int
    {
        $result = $this->gateway->delete(['id' => $id]);
        if ($result) {
            $this->optionsLookup->delete(['productId' => $id]);
        }
        return $result;
    }
}