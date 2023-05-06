<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\AbstractModel;
use App\Model\ModelInterface;
use App\Model\ModelTrait;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Filter\HtmlEntities;
use Laminas\Filter\StripTags;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Filter\ToInt;
use Laminas\Filter\UpperCaseWords;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\Validator\StringLength;
use Store\Db\TableGateway\CategoriesTable;
use Store\Model\Product;

use function count;
use function explode;
use function is_array;
use function strpos;

final class Category extends AbstractModel implements ModelInterface
{
    use ModelTrait;

    /** @var ResultSet|array $children */
    protected $children;

    /** @var string $p */
    private $p;
    /** @var string $i */
    private $i;
    /** @var string $t */
    private $t;

    public function __construct(?Product $product, ?array $config, ?CategoriesTable $categoriesTable = null)
    {
        parent::__construct([], $config);
        if ($categoriesTable !== null) {
            $this->gateway = $categoriesTable;
            $this->product = $product->getGateway();
            $this->p = $this->product->getTable();
            $this->i = $config['db']['store_image_table_name'];
            $this->t = $this->gateway->getTable();
        }
    }

    public function fetchMenu(bool $fetchArray = true): ResultSetInterface|array
    {
        $where = new Where();
        $where->equalTo('active', 1);
        $where->notEqualTo('isBundle', 1);
        if (! $fetchArray) {
            return $this->gateway->select($where);
        }
        return $this->gateway->select($where)->toArray();
    }

    public function fetchAllWithProductCountAndImages(
        bool $forHome = false,
        bool $withBundles = false,
        bool $fetchArray = true
        ): ResultSetInterface|array {

        $where = new Where();
        $select = $this->gateway->getSql()->select();
        $select->join(
            $this->p,
            $this->p . '.category = ' . $this->t . '.label',
            ['category'],
        );
        $select->join(
            $this->i,
            $this->t . '.id = ' . $this->i . '.categoryId',
            ['fileName'],
            Select::JOIN_LEFT_OUTER,
        );
        $select->group([$this->t . '.label', $this->i . '.type']);
        $select->columns([
            'productCount' => new Expression('COUNT(category)'),
            'id',
            'title',
            'description'
        ]);
        // Limit this to only categories that should be shown onHome
        if ($forHome) {
            $where->equalTo($this->t . '.onHome', 1);
        }
        // should we include categories that are bundles?
        if ($withBundles) {
            $where->equalTo('isBundle', 1);
        } else {
            $where = new Where(null, PredicateSet::COMBINED_BY_OR);
            $where->equalTo('isBundle', 0);
            $where->isNull('isBundle');
        }
        $select->where($where);
        $result = $this->gateway->selectWith($select);
        // send an array
        if ($fetchArray) {
            return $result->toArray();
        }
        return $result;
    }

    public function fetchSelectValueOptions(bool $idAsValue = true, bool $fetchBundles = false)
    {
        $where = new Where();
        if ($fetchBundles) {
            if (! $idAsValue) {
                //$idAsValue = true; // if were getting bundles then we just need the ids as values
            }
            $where->equalTo('isBundle', 1);
            $resultSet = $this->gateway->select($where);
        } else {
            $where = new Where(null, PredicateSet::COMBINED_BY_OR);
            $where->equalTo('isBundle', 0);
            $where->isNull('isBundle');
            $resultSet = $this->gateway->select($where);
        }
        $data = [];
        $i = 0;
        foreach($resultSet as $row) {
            if ($idAsValue) {
                $data[$row->id] = $row->label;
            } else {
                ++$i;
                $data[$i]['label'] = $row->label;
                $data[$i]['value'] = $row->label;
            }
        }
        return $data;
    }

    /**
     * $model replaces $set as the set is derived from model data
     * @param string|array|Closure $where
     * @param  null|array $joins
     * @return int
     */
    public function save(Category|array $set, ?array $where = null, ?array $joins = null): int
    {
        if ($set instanceof Category) {
            $set = $set->getArrayCopy();
        }

        if ($set['parentId'] === 'No Parent' || $set['parentId'] === '') {
            unset($set['parentId']);
        }

        if (isset($set['id']) && $where !== null) {
            return $this->gateway->update($set, $where, $joins);
        } else {
            return $this->gateway->insert($set);
        }
    }

    public function fetchByTitleWithChildren(string $title, $fetchArray = true): ResultSetInterface|array
    {

    }

    /** @deprecated */
    public function fetchAllWithChildren(bool $onlyActive = true, bool $fetchArray = true): ResultSetInterface|array
    {
        $where = new Where();
        $categories = $this->gateway->select()->toArray();
        $count = count($categories);

        for ($i=0; $i < $count; $i++) {
            $children = $this->fetchChildren($categories[$i]['id'])->toArray();
            if (count($children) >= 1) {
                $categories[$i]['children'] = $children;
            } else {
                $categories[$i]['children'] = null;
            }
        }
        return $categories;
    }

    public function fetchChildren(int $parentId)
    {
        /** @var Sql $sql */
        $sql = $this->gateway->getSql();
        $select = $sql->select();
        $select->where(function(Where $where) use ($parentId) {
            $where->equalTo('parentId', $parentId);
        });
        return $this->gateway->selectWith($select);
    }

    public function fetchBundles(bool $fetchProducts = true, $fetchArray = true): ResultSetInterface|array
    {
        $where  = new Where();
        $select = $this->gateway->getSql()->select();
        $where->isNotNull('parentId');
        $where->equalTo('isBundle', 1);

        $select->join(
            $this->p,
            $this->p . '.bundleLabel = ' . $this->t . '.label',
            [
                'id', 'userId', 'bundleLabel', 'category', 'label', 'title', 'description', 'cost', 'weight', 'manufacturer', 'sku',
                'createdDate', 'active', 'onSale', 'discount', 'saleStartDate', 'saleEndDate', 'onHome', 'data'
            ],
            Select::JOIN_RIGHT_OUTER
            //Select::JOIN_LEFT_OUTER,
        );

        $select->join(
            $this->i,
            $this->t . '.id = ' . $this->i . '.categoryId',
            ['categoryImage' => 'fileName'],
            //Select::JOIN_LEFT,
        );

        $select->columns([
            'categoryLabel'    => 'label',
            'categoryId'       => 'id',
            'categoryTitle'    => 'title',
        ]);
        $result = $this->gateway->select($where);
        if (! $fetchArray) {
            return $result;
        }
        return $result->toArray();
    }

    public function fetchAllProductsByMultiColumns(
        bool $fetchArray,
        bool $onlyActive,
        ...$columns
        ): ResultSetInterface|array {

        $where = new Where();
        // ...columns are expected to be columns from the category table
        // the could be also be from the joined tables but they would need to be be of the form tableName.columnName => $value
        if (ArrayUtils::isList($columns)) {
            foreach ($columns as $key => $whereArray) {
                foreach ($whereArray as $column => $value) {
                    $where->equalTo(
                        (strpos($column, '.') !== false) ? $column : $this->t . '.' . $column,
                        $value
                    );
                }
            }
        }
        // if ($onlyActive) {
        //     $where->equalTo($this->t . '.active', 1);
        //     $where->equalTo($this->p . '.active', 1);
        // }
        $select = new Select();
        $select->from(['c' => $this->t]);
        $select->join(
            ['p' => $this->p],
            'c.label = p.category',
            [
                'id', 'userId', 'bundleLabel', 'category',
                //'imgCount' => new Expression('COUNT("fileName") >= 1')
            ]
        );
        $select->join(
            ['i' => $this->i],
            'p.id = i.productId',
            [
                'fileName',
            ],
            Select::JOIN_RIGHT
        );
        $where->greaterThanOrEqualTo('p.id', 0);
        $select->columns([
            'label'
        ]);
        //$select->order(['c.label ASC', 'p.label ASC', 'i.fileName ASC']);
        $select->where($where);
        $str = $select->getSqlString();
        if (! $fetchArray) {
            return $this->gateway->selectWith($select);
        }
        return $this->gateway->selectWith($select)->toArray();
    }

    public function fetchByTitleWithAllProducts(
        string $title,
        $fetchArray = true,
        bool $onlyActive = true,
        bool $onSale = true,
        bool $isBundle = false
    ): ResultSetInterface|array {
        // TODO: Fixme This is kind hacky and will be fixed in the next iteration, were looking for bundle in the title..... I know, hacky
        $search = strpos($title, 'bundle');
        if (! $search === false && ! $isBundle) {
            $isBundle = true;
        }
        $where = new Where();
        $where->equalTo($this->t . '.title', $title);

        if ($onlyActive) {
            $where->equalTo($this->t . '.active', 1);
            $where->equalTo($this->p . '.active', 1);
        }
        $where->greaterThanOrEqualTo($this->p .'.id', 0);
        $select = $this->gateway->getSql()->select();
        $select->join(
            $this->p,
            ! $isBundle ? $this->p . '.category = ' . $this->t . '.label' : $this->p . '.bundleLabel = ' . $this->t . '.label',
            [
                'id', 'userId', 'bundleLabel', 'category', 'label', 'title', 'description', 'cost', 'weight', 'manufacturer', 'sku',
                'createdDate', 'active', 'onSale', 'discount', 'saleStartDate', 'saleEndDate', 'onHome', 'data'
            ],
            Select::JOIN_RIGHT_OUTER
            //Select::JOIN_LEFT_OUTER,
        );
        $select->join(
            $this->i,
            $this->p . '.id = ' . $this->i . '.productId',
            ['productImage' => 'fileName'],
            //Select::JOIN_LEFT,
        );
        $select->columns([
            'categoryLabel'    => 'label',
            'categoryId'       => 'id',
            'categoryTitle'    => 'title',
            'parentCategoryId' => 'parentId',
        ]);
        $select->where($where);
        if (! $fetchArray) {
            return $this->gateway->selectWith($select);
        }
        return $this->gateway->selectWith($select)->toArray();
    }

    public function fetchTreeStore(int|string $catId, string $idColumn = 'label')
    {
        $select = $this->gateway->getSql()->select();
        $select->columns(['id', 'label', ]);
    }

    public function fetchGridsStore(bool $filterBundles = true, bool $fetchArray = true): array
    {
        $where  = new Where();
        $where->notEqualTo('title', 'bundles');
        if ($filterBundles) {
            $where->isNull('isBundle');
            $where->or->equalTo('isBundle', 0);
        }
        $result = $this->gateway->select($where);
        if (! $fetchArray) {
            return $result;
        }
        return $result->toArray();
    }

    public function getInputFilter()
    {
        // if (!$this->inputFilter) {
        //     $this->inputFilter = new $this->inputFilterClass();
        // }
        // $this->inputFilter->add([
        //         'name' => 'label',
        //         'required' => false,
        //         'allow_empty' => true,
        //         'continue_if_empty' => true,
        //         'filters' => [
        //             ['name' => StripTags::class],
        //             ['name' => StringTrim::class],
        //             ['name' => UpperCaseWords::class],
        //         ],
        //         'validators' => [
        //             [
        //                 'name'    => StringLength::class,
        //                 'options' => [
        //                     'encoding' => 'UTF-8',
        //                     'min'      => 1,
        //                     'max'      => 255,
        //                 ],
        //             ],
        //             [
        //                 'name'    => NoRecordExists::class,
        //                 'options' => [
        //                     'table' => 'store_categories',
        //                     'field' => 'label',
        //                     'dbAdapter' => $this->gateway->getAdapter(),
        //                     'messages' => [
        //                         NoRecordExists::ERROR_RECORD_FOUND => 'Category by that name is already in use!',
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ]);
        // return $this->inputFilter;
    }
}
