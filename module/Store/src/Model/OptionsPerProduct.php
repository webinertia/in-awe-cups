<?php

declare(strict_types=1);

namespace Store\Model;


use App\Model\AbstractModel;
use App\Model\ModelInterface;
use App\Model\ModelTrait;
use ArrayObject;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\ArrayObject as Ao;
use Laminas\Stdlib\ArrayUtils;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\TryCatch;
use Store\Model\Product;
use Store\Model\ProductOptions;

use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_values;
use function count;
use function explode;

class OptionsPerProduct extends AbstractModel implements ModelInterface
{
    use ModelTrait;

    /** @var string $resourceId */
    protected $resourceId = 'store';
    /** @var AdapterPluginManager $adapterManager */
    protected $adapterManager;
    /** @var Paginator $paginator */
    protected $paginator;
    /** @var bool $paginated */
    protected $paginated;
    /** @var int|string $itemCountPerPage */
    protected $itemCountPerPage;
    /** @var TableGateway $productTable */
    protected $productTable;
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var TableGateway $gateway */
    protected $gateway;
    /** @var int|float $step */
    protected $step;
    /** @var string $p */
    protected $p;
    /** @var string $c */
    protected $c;
    /** @var string $t */
    protected $t;
    /** @var string $i */
    protected $i;

    public function __construct(
        ?ProductOptions $productOptions = null,
        ?TableGateway $gateway = null,
        ?array $config = null,
        ?AdapterPluginManager $adapterManager = null
        ) {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
        if ($productOptions !== null) {
            $this->productOptions = $productOptions;
        }
        if ($adapterManager !== null) {
            $this->adapterManager = $adapterManager;
        }
        if ($config !== null) {
            $this->config    = $config;
            $this->t         = $this->config['db']['store_options_per_product_table_name'];
            $this->c         = $this->config['db']['store_categories_table_name'];
            $this->p         = $this->config['db']['products_table_name'];
            $this->i         = $this->config['db']['store_image_table_name'];
            $this->step      = $this->config['module_settings']['store']['search_options']['price_step_value'];
            $this->paginated = $this->config['module_settings']['store']['pagination']['enabled'];
            $this->itemCountPerPage = $this->config['module_settings']['store']['pagination']['items_per_page'];
        }
        $this->productTable = new TableGateway('store_products', $this->gateway->getAdapter());
    }

    public function productSearch(
        ?int $productId,
        ?string $category,
        array $params
    ): Paginator {
        return $this->search($productId, $category, $this->sortParams($params), null);
    }

    private function sortParams(array $params): array
    {
        $inGroups  = [];
        $inOptions = [];
        $costRange = [];
        $searchOptions = [];
        // filter out everything except what is in the option lookup table
        if (isset($params['cost']) && $params['cost'] !== 'all') {
            $costRange['min']    = (float) $params['cost'] - $this->step;
            $costRange['max']    = (float) $params['cost'];
            unset($params['cost']);
            $searchOptions['costRange'] = $costRange;
        } else {
            unset($params['cost']);
        }
        foreach($params as $group => $options) {
            $inGroups = array_keys($params);
            //$inOptions[] = array_values($options);
            foreach ($options as $k => $v) {
                $inOptions[] = $v;
            }
        }
        if ($inGroups !== []) {
            $searchOptions['groupSet']  = $inGroups;
        }
        if ($inGroups !== []) {
            $searchOptions['optionSet'] = $inOptions;
        }
        return $searchOptions;
    }

    protected function search(
        ?int $productId,
        ?string $category,
        ?array $params,
        ?array $sort,
        bool $onlyActive = true
    ): Paginator {

        $select = new Select();
        $where  = new Where();
        $select->from(['o' => $this->t])
        ->columns(['productId', 'optionGroup', 'option'])
        ->quantifier(Select::QUANTIFIER_DISTINCT);

        if ($productId !== null) {
            $where->equalTo('productId', $productId);
        }
        if ($category !== null) {
            $where->equalTo('o.category', $category);
        }
        if (isset($params['costRange'])) {
            $where->between('o.cost', $params['costRange']['min'], $params['costRange']['max']);
        }
        if (isset($params['groupSet'])) {
            //$where->in('optionGroup', $params['groupSet']);
            // foreach ($params['groupSet'] as $optionGroup) {
            //     $where->or->equalTo('optionGroup', $optionGroup);
            // }
        }
        if (isset($params['optionSet'])) {

            $where->in('option', $params['optionSet']);
            // foreach ($params['optionSet'] as $option) {
            //     $where->like('option', $option);
            // }
        }

        $select->join(
            ['p' => $this->p],
            'o.productId = p.id',
            ['*'],
        );
        $select->join(
            ['c' => $this->c],
            'o.category = c.label',
            ['categoryId' => 'id', 'categoryTitle' => 'title']
        );
        $select->join(
            ['i' => $this->i],
            'i.productId = o.productId',
            ['fileName']
        );
        if ($onlyActive) {
            $where->equalTo('c.active', 1);
            $where->equalTo('p.active', 1);
        }
        $select->group(['o.productId']);
        if ($sort !== null) {

        }
        //$query = $select->getSqlString();
        $select->where($where);
        $adapter   = $this->adapterManager->get(DbSelect::class, [$select, $this->gateway->getSql()]);
        $paginator = new Paginator($adapter);
        //$paginator->setDefaultItemCountPerPage($this->paginated ? $this->itemCountPerPage : $paginator->getTotalItemCount());
        return $paginator;
    }

    public function fetchOptionsByProductId(int $productId): array
    {
        $data = [];
        $this->where->equalTo('productId', $productId);
        $this->select->from($this->t);
        $this->select->columns(['optionGroup']);
        $this->select->where($this->where);
        $this->select->quantifier(Select::QUANTIFIER_DISTINCT);
        $groups = $this->gateway->selectWith($this->select)->toArray();

        foreach ($groups as $group) {
            $this->select->where(
                (new Where())->equalTo('optionGroup', $group['optionGroup'])->equalTo('productId', $productId)
            );
            $this->select->columns(['option', 'cost']);
            $data[$group['optionGroup']] = $this->gateway->selectWith($this->select)->toArray();
        }
        return $data;
    }

    public function fetchByOptionGroup(
        int|string $productId,
        string $optionGroup,
        bool $fetchArray = true,
        ?array $columns = null
    ): array|ResultSetInterface {
        $select = $this->gateway->getSql()->select();
        if ($columns !== null) {
            $select->columns($columns);
        }
        $select->where(['productId' => $productId])->where(['optionGroup' => $optionGroup]);
        $resultSet = $this->gateway->selectWith($select);
        if (! $fetchArray) {
            return $resultSet;
        }
        return $resultSet->toArray();
    }

    public function fetchProductCountByOption(string $category, string $option): int|string
    {
        $this->select->from($this->t);
        $this->select->where((new Where())->equalTo('category', $category)->equalTo('option', $option));
        $count = count($this->gateway->selectWith($this->select)->toArray());
        return $count;
    }

    public function fetchMultiCheckboxValues(
        int|string $productId,
        string $category,
        string $optionGroup,
        bool $idAsValue = false
    ): array {
        $mergedOptions      = [];
        $temp               = [];
        $columns            = ['id', 'category', 'optionGroup', 'option'];
        $totalOptions       = $this->productOptions->fetchOptions($category, $optionGroup, true, $columns);
        $totalOptionCount   = count($totalOptions);
        $currentOptions     = $this->fetchByOptionGroup($productId, $optionGroup, true, $columns);
        $currentOptionCount = count($currentOptions);

        if ($currentOptionCount <= $totalOptionCount && $currentOptionCount > 0) {
            for ($i=0; $i < $totalOptionCount; $i++) {
                foreach($currentOptions as $option) {
                    if ($option['option'] === $totalOptions[$i]['option']) {
                        $mergedOptions[$i]['value'] = $idAsValue ? $option['id'] : $option['option'];
                        $mergedOptions[$i]['selected'] = true;
                        $mergedOptions[$i]['label'] = $option['option'];
                        break;
                    } else {
                        $mergedOptions[$i]['value'] = $idAsValue ? $totalOptions[$i]['id'] : $totalOptions[$i]['option'];
                        $mergedOptions[$i]['selected'] = false;
                        $mergedOptions[$i]['label'] = $totalOptions[$i]['option'];
                    }
                }
            }
        } elseif ($currentOptionCount === 0) {
            for ($i=0; $i < $totalOptionCount; $i++) {
                $mergedOptions[$i]['value'] = $idAsValue ? $totalOptions[$i]['id'] : $totalOptions[$i]['option'];
                $mergedOptions[$i]['selected'] = false;
                $mergedOptions[$i]['label'] = $totalOptions[$i]['option'];
            }
        }
        return $mergedOptions;
    }

    /**
     * We only want to allow filtering based on options that have actually been assigned
     * to a particular product that belongs to a particular category
     * @param string $category
     * @param bool $fetchArray
     * @return ResultSetInterface|array
     * @throws ExceptionInvalidArgumentException
     * @throws RuntimeException
     */
    public function fetchSearchableOptions(string $category, bool $fetchArray = true): Ao|array
    {
        $where  = new Where();
        $where->equalTo('category', $category);
        $select = $this->gateway->getSql()->select();
        $select->columns(
            [
                'optionGroup',
                'option',
            ]
        );
        $select->order('optionGroup');
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->where($where);
        $groups = $this->gateway->selectWith($select)->toArray();
        $return = [];
        foreach ($groups as $option) {
            $return[$option['optionGroup']][] = $option['option'];
        }
        if ($fetchArray) {
            return $return;
        }
        return new Ao($return, Ao::ARRAY_AS_PROPS);
    }

    public function update(array $set): int
    {
        $where = new Where();
        $optionId = $set['id'];
        unset($set['id']);
        $where->equalTo('optionId', $optionId);
        $select = $this->gateway->getSql()->select();
        $select->where($where);
        $resultCount = 0;
        try {
            $records = $this->gateway->selectWith($select);
            if (count($records) > 0) {
                foreach ($records as $record) {
                    $resultCount = $resultCount + (int) $this->gateway->update($set, ['id' => $record->id]);
                }
                return $resultCount;
            } else {
                return 1;
            }
        } catch(\Throwable $th) {
            throw $th;
        }
        // return $this->gateway->update($set, ['optionId' => $set['id']]);
    }

    public function save($set)
    {
        if (ArrayUtils::isList($set['productOptions'])) {
            $pSelect = $this->productTable->getSql()->select();
            $pSelect->columns(['category']);
            $pWhere  = new Where();
            $pWhere->equalTo('id', $set['productId']);
            $pSelect->where($pWhere);
            try {

            } catch (\Throwable $th) {

            }
            foreach ($set['productOptions'] as $key => $value) {
                $optionData = $this->productOptions->fetchByColumn('id', $value);
                if (! $this->hasRecord($optionData['productId'], $optionData['optionGroup'], $optionData['option'])) {
                    $category = $this->productTable->selectWith($pSelect)->current()->category;
                    $this->gateway->insert([
                        'productId'   => $set['productId'],
                        'category'    => $category,
                        'optionGroup' => $optionData['optionGroup'],
                        'option'      => $optionData['option'],
                        'cost'        => $optionData['cost'],
                        'optionId'    => $optionData['id'],
                    ]);
                }
            }
        }
    }

    public function hasRecord($productId, $optionGroup, $option, $returnRecord = false): bool|self
    {
        $where = new Where();
        $where->equalTo('productId', $productId)->equalTo('optionGroup', $optionGroup)->equalTo('option', $option);
        $check = $this->gateway->select($where);
        if ($check instanceof ResultSet) {
            $result = $check->current();
        }
        if ($result === null) {
            return false;
        }
        if($result instanceof self || $result instanceof ArrayObject) {
            if ($returnRecord) {
                return $result;
            }
            return true;
        }
    }
}
