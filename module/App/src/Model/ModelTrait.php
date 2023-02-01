<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\ModelInterface;
use ArrayObject;
use Closure;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Exception\RuntimeException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Exception\InvalidArgumentException;
use Laminas\Db\TableGateway\Exception\RuntimeException as TableGatewayRuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Stdlib\ArrayUtils;

/**
 * AbstractModel
 * Trait method signatures for static analysis
 * @codingStandardsIgnoreStart
 * @method \Laminas\Db\TableGateway\AbstractTableGateway getAdapter()
 * @codingStandardsIgnoreEnd
 */

use function sprintf;

trait ModelTrait
{
    /** @var AbstractTableGateway $gateway */
    protected $gateway;
    /** @var ResultSet $resultSet */
    protected $resultSet;
    /** @var string $resourceId */
    //protected $resourceId;
    /** @var int|string $ownerId */
    protected $ownerId;

    /**
     * $model replaces $set as the set is derived from model data
     * @param string|array|Closure $where
     * @param  null|array $joins
     * @return int
     */
    public function save($model, $where = null, ?array $joins = null): int
    {
        $set = $model->getArrayCopy();
        if (isset($model->id)) {
            $result = $this->gateway->update($set, $where, $joins);
        } else {
            $result = $this->gateway->insert($set);
        }
        return $result;
    }

    /** @throws RuntimeException */
    public function fetchByColumn(string $column, mixed $value, bool $fetchResultSet = false): ResultSetInterface|self
    {
        $column    = (string) $column;
        $resultSet = $this->gateway->select([$column => $value]);
        if ($fetchResultSet) {
            return $resultSet;
        }
        $row       = $resultSet->current();
        if (! $row) {
            throw new RuntimeException(sprintf('Could not fetch column: ' . $column . ' with value: ' . $value));
        }
        return $row;
    }

    /**
     * @param string $column
     * @param int|string $value
     * @param null|array $columns
     * @throws TableGatewayRuntimeException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function fetchColumns($column, $value, ?array $columns = ['*'], ?bool $fetchArray = true): self
    {
        $select = $this->gateway->getSql()->select();
        $select->columns($columns);
        $select->where([$column => $value]);
        $resultSet = $this->gateway->selectWith($select);
        if ($fetchArray) {
            return $resultSet->toArray();
        }
        return $resultSet;
    }

    public function fetchAll($fetchArray = false): ResultSetInterface|array
    {
        if ($fetchArray) {
            return $this->gateway->select()->toArray();
        }
        return $this->gateway->select();
    }

    public function recordExists(array $data): bool
    {
        if (ArrayUtils::hasNumericKeys($data) || $data === []) {
            throw new InvalidArgumentException('$data must be a non empty associative array with only string keys');
        }
        $where  = new Where();
        foreach ($data as $column => $value) {
            $where->equalTo($column, $value);
        }
        $check = $this->gateway->select($where);
        $result = $check->current();
        if($result) {
            return true;
        } else {
            return false;
        }
    }

    public function noRecordExists(array $data): bool
    {
        if (ArrayUtils::hasNumericKeys($data) || $data === []) {
            throw new InvalidArgumentException('$data must be a non empty associative array with only string keys');
        }
        $where  = new Where();
        foreach ($data as $column => $value) {
            $where->equalTo($column, $value);
        }
        $check = $this->gateway->select($where);
        $result = $check->current();
        if($result) {
            return false;
        } else {
            return true;
        }
    }

    public function getLastInsertId(): int|string
    {
        return $this->gateway->getLastInsertValue();
    }

    public function delete(Where|Closure|array $where): int
    {
        return $this->gateway->delete($where);
    }

    public function getResourceId(): string
    {
        return $this->resourceId ?? static::class;
    }

    public function getOwnerId(): int|string
    {
        return $this->ownerId ?? $this->offsetGet('ownerId') ?? $this->offsetGet('userId');
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->gateway->getAdapter();
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }

    public function getTable(): string
    {
        return $this->gateway->getTable();
    }

    public function getGateway(): TableGatewayInterface
    {
        return $this->gateway;
    }
}
