<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\AbstractModel;
use App\Model\ModelInterface;
use App\Model\ModelTrait;
use Dojo\Data;
use Laminas\Db\Exception\InvalidArgumentException;
use Laminas\Db\ResultSet\AbstractResultSet;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\Exception\RuntimeException;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\Validator\Db\RecordExists;
use Store\Db\TableGateway\ProductOptionsTable;
use Store\Model\Exception\OptionExistsException;

use function is_array;
use function implode;

class ProductOptions extends AbstractModel implements ModelInterface
{
    use ModelTrait;

    protected const SAVE_UPDATED = 202;
    protected const SAVE_CREATED = 201;
    /** @var ProductOptionsTable $gateway */
    protected $gateway;
    /** @var Where $where */
    protected $where;

    public function __construct(?ProductOptionsTable $productOptionsTable = null)
    {
        parent::__construct([]);
        if ($productOptionsTable !== null) {
            $this->gateway = $productOptionsTable;
        }
    }
    // create and return dojo data object
    public function fetchGrid(): Data
    {
        $dojoData = new Data();
        $dojoData->setIdentifier('id');

        /** @var Where $where */
        $this->where  = new Where();
        $this->where->greaterThanOrEqualTo('id', 1);
        $select = $this->gateway->getSql()->select();
        $select->order(['category ASC', 'optionGroup ASC', 'option ASC']);
        $select->where($this->where);
        $dojoData->setItems($this->gateway->selectWith($select)->toArray());
        return $dojoData;
    }

    public function fetchOptions(
        ?string $category = null,
        ?string $optionGroup = null,
        bool $fetchArray = true,
        ?array $columns = null
    ): array|ResultSetInterface {
        $result = null;
        $select = $this->gateway->getSql()->select();
        if ($columns !== null) {
            $select->columns($columns);
        }
        if ($category === null && $optionGroup === null) {
            /** @var ResultSet $result */
            $result = $this->gateway->select();
        } else {
            if ($category !== null) {
                $select->where(['category' => $category]);
            }
            if ($optionGroup !== null) {
                $select->where(['optionGroup' => $optionGroup]);
            }
            /** @var ResultSet $result */
            $result = $this->gateway->selectWith($select);
        }
        if (! $fetchArray) {
            return $result;
        }
        if ($result instanceof ResultSetInterface || $result instanceof ModelInterface) {
            return $result->toArray();
        }
        return $result; // should return null at this point
    }

    public function fetchOptionsById(int $id)
    {

    }

    public function fetchByOptionGroup(string $optionGroup)
    {

    }

    public function fetchByCategory(string $category)
    {

    }

    /** @deprecated */
    public function fetchSearchableOptions(string $category, bool $fetchArray = true): ResultSetInterface|array
    {
        $data = [];
        $lookup = 'store_options_per_product';
        $t      = $this->gateway->getTable();
        $where  = new Where();
        $where->equalTo('o.category', $category);
        $select = $this->gateway->getSql()->select();
        $select->where($where);
        $select->columns(['optionGroup']);
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->join(
            ['o' => $t],
           // $t.'.optionGroup = o.optionGroup',
            'o.optionGroup = ' . $t .'.optionGroup',
            ['option'],
            Select::JOIN_LEFT_OUTER
        );
        $select->join(
            ['l' => $lookup],
            'o.option = l.option',
            ['productCount' => new Expression('COUNT(i.productId)')]
        );
        $select->order(['optionGroup']);
        $groups = $this->gateway->selectWith($select)->toArray();
        $return = [];
        foreach ($groups as $option) {
            $return[$option['optionGroup']][] = $option['option'];
        }
        return $groups;
    }

    public function fetchOptionGroups($fetchArray = false)
    {
        $select = $this->gateway->getSql()->select();
        $select->columns(['optionGroup']);
        $select->group('optionGroup');
        $result = $this->gateway->selectWith($select);
        if ($fetchArray) {
            return $result->toArray();
        }
        return $result;
    }

    public function fetchByHashTable(array $map, array|string $orderBy = null, $fetchArray = true): ResultSetInterface|array
    {
        if (ArrayUtils::isHashTable($map)) {
            $where  = new Where();
            $select = $this->gateway->getSql()->select();
            foreach ($map as $column => $value) {
                $where->equalTo($column, $value);
            }
            if (!empty($orderBy)) {
                $select->order($orderBy);
            }
            $select->where($where);
            $result = $this->gateway->selectWith($select);
            if ($fetchArray && $result instanceof ResultSetInterface || $result instanceof self) {
                return $result->toArray();
            }
            return $result;
        }
    }

    /**
     * @param array<string, int|string> $set
     * @param null|array|Closure $where
     * @param null|array $joins
     * @return array<mixed> $return
     */
    public function save($set, ?array $joins = null): int
    {
        if (isset($set['id'])) {
            return $this->gateway->update($set, ['id' => $set['id']]);
        } else {
            return $this->gateway->insert($set);
        }
    }
}
