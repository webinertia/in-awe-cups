<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\AbstractModel;
use App\Log\LogEvent;
use App\Model\ModelTrait;
use App\Upload\UploadHandlerInterface;
use Exception as GlobalException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\Exception\RuntimeException;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\ArrayUtils;
use Store\Db\TableGateway\ImageTable;
use Store\Model\Exception;
use User\Service\UserService;

use const ARRAY_FILTER_USE_KEY;

use function array_flip;
use function array_filter;
use function array_intersect_key;
use function count;
use function file_exists;
use function is_array;
use function sprintf;
use function strlen;
use function strpos;
use function unlink;

final class Image extends AbstractModel implements UploadHandlerInterface
{
    use ModelTrait;

    public const IMAGE_TARGET_PATH = __DIR__ . '/../../../../public/modules/store/%s/images';
    public const CATEGORY_TYPE     = 'category';
    public const PRODUCT_TYPE      = 'product';
    public const FILTER_KEYS       = [
        'image',
        'imageFile',
        'image-file',
        'file',
        'images',
        'imageFiles',
        'image-files',
        'files',
    ];

    /** @var BaseName $baseName */
    protected $baseName;
    /** @var RenameUpload $renameUpload */
    protected $renameUpload;
    /** @var null|string $uploadType */
    protected $uploadType;
    /** @var array<int, string> $deletableKeys */
    protected $deletableKeys = ['id', 'productId', 'categoryId'];
    /** @var string $targetFileName */
    protected $targetFileName = '%sImage';
    /** @var string $target */
    protected $target;
    /** @var AdapterPluginManager $adapterManager */
    protected $adapterManager;
    /** @var Paginator $paginator */
    protected $paginator;
    /** @var bool $paginated */
    protected $paginated;
    /** @var int|string $itemCountPerPage */
    protected $itemCountPerPage;
    /** @var array<int, array> $persistedData */
    protected $persistedData = [];
    /** @var array<mixed> $fileData */
    protected $fileData;
    /** @var string $p */
    protected $p;
    /** @var string $c */
    protected $c;
    /** @var string $t */
    protected $t;

    public function __construct(
        UserService $userService,
        RenameUpload $renameUpload,
        BaseName $baseName,
        ?ImageTable $imageTable = null,
        array $config = [],
        ?AdapterPluginManager $adapterManager = null
    ) {
        parent::__construct([], $config);
        $this->userId       = $userService->id;
        $this->renameUpload = $renameUpload;
        $this->baseName     = $baseName;
        if ($imageTable !== null) {
            $this->gateway = $imageTable;
            $this->t       = $this->gateway->getTable();
        }
        if ($adapterManager !== null) {
            $this->adapterManager = $adapterManager;
        }
        if ($config !== []) {
            $this->config = $config;
            $this->c = $this->config['db']['store_categories_table_name'];
            $this->p = $this->config['db']['products_table_name'];
            $this->paginated = $this->config['module_settings']['store']['pagination']['enabled'];
            $this->itemCountPerPage = $this->config['module_settings']['store']['pagination']['items_per_page'];
        }
    }

    public function fetchProductImagesById(int $productId, ?array $columns = ['fileName'], ?bool $fetchArray = true): ResultSet|array
    {
        $where = new Where();
        $where->equalTo('productId', $productId)->equalTo('active', 1);
        $select = new Select();
        $select->from($this->gateway->getTable())->columns($columns)->where($where);
        $result = $this->gateway->selectWith($select);
        if ($fetchArray) {
            return $result->toArray();
        }
        return $result;
    }

    /**
     * ...$columns['alias.column' => $value] possible aliases are as follows
     * i - store_images
     * p - store_products
     * c - store_categories
     * o - store_options_per_product
     *
     * @param bool $fetchArray
     * @param bool $onlyActive
     * @param mixed $columns
     * @return ResultSetInterface|array
     * @throws Exception\InvalidArgumentException
     */
    public function fetchAllProductsByMultiColumns(
        bool $onlyActive,
        ...$columns
        ): Paginator {

        if (ArrayUtils::isList($columns)) {
            $where = new Where();
            foreach ($columns as $predicate) {
                if (ArrayUtils::hasStringKeys($predicate) && ArrayUtils::isHashTable($predicate)) {
                    foreach ($predicate as $column => $value) {
                        $where->equalTo($column, $value);
                    }
                } else {
                    throw new Exception\InvalidArgumentException('$predicate array must be structured as StdLib\ArrayUtils:: hasStringKeys & isHashTable');
                }
            }
            if ($onlyActive) {
                $where->equalTo('p.active', 1);
            }
            $select = new Select();
            // alias for store_images table
            $select->from(['i' => $this->getTable()])->columns([
                'productId', 'productTitle', 'categoryId', 'categoryTitle', 'fileName', 'uploadedTime'
            ]);

            //join store_products as p
            $select->join(
                ['p' => 'store_products'],
                'productId = p.id',
            );
            // group prevents duplicates from multiple images
            $select->group(['p.id']);
            $select->order(['label ASC', 'productId ASC']);
            $select->where($where);
            $paginator = new Paginator($this->adapterManager->get(DbSelect::class, [$select, $this->gateway->getSql()]));
            $paginator->setDefaultItemCountPerPage(
                $this->paginated ? $this->itemCountPerPage : $paginator->getTotalItemCount()
            );
            return $paginator;
        } else {
            throw new Exception\InvalidArgumentException('Expects ...$columns to be ArrayUtils::isList acceptable');
        }
    }

    public function handleUpload(array $fileData)
    {
        $this->exchangeArray($fileData);

        $fileCount = count($this->fileData);
        try {
            for ($i = 0; $i < $fileCount; $i++) {
                $renamed = $this->renameUpload->filter($this->fileData[$i]);
                if ($renamed['size'] > 0 && $renamed['error'] === 0) {
                    $this->fileName = $this->baseName->filter($renamed['tmp_name']);
                    if (! (strlen($this->fileName) >= 5 && $this->save($this))) {
                        throw new Exception\ImageManagerException('Image could not be processed');
                    }
                }
            }
            return true;
        } catch (\Throwable $th) {
            $this->getEventManager()->trigger(LogEvent::ERROR, $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return false;
        }
    }

    /**
     * @param $fileData
     * */
    public function handleDelete(array $fileData)
    {
        try {
            //$this->exchangeArray($fileData);
            $records = $this->fetchInternal($fileData);
            // this was skipped, TODO: Debug
            foreach ($records as $record) {
                $target = sprintf(self::IMAGE_TARGET_PATH, $record->getUploadType()) . '/' . $record->fileName;
                if (file_exists($target)) {
                    if (unlink($target)) {
                        if (! $this->delete(['id' => $record->id])) {
                            throw new Exception\ImageManagerException('Image with file name: ' . $record->fileName . ' could not be deleted');
                        }
                    }
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

    private function fetchInternal(array $predicates, $fetchArray = false)
    {
        // TODO
        $where = new Where();
        foreach ($predicates as $column => $value) {
            $where->equalTo($column, $value);
        }
        $records = $this->gateway->select($where);
        if ($fetchArray) {
            return $records->toArray();
        }
        return $records;
    }

    public function exchangeArray($data)
    {
        if (isset($data['productId'])) {
            $this->setUploadType(self::PRODUCT_TYPE);
        }
        if (isset($data['categoryId'])) {
            $this->setUploadType(self::CATEGORY_TYPE);
        }
        if (isset($data['submit'])) {
            unset($data['submit']);
        }
        $filtered = array_intersect_key($data, array_flip(self::FILTER_KEYS));
        if (is_array($filtered) && $filtered !== []) {
            foreach ($filtered as $key => $value) {
                $this->fileData = $value;
                unset($data[$key]);
            }
        }
        unset($filtered);

        parent::exchangeArray($data);
    }

    public function setUploadType(null|string $type): void
    {
        $this->uploadType = $type;
        $this->setTarget(sprintf(self::IMAGE_TARGET_PATH, $type));
        $this->renameUpload->setTarget($this->target);
    }

    public function getUploadType(): null|string
    {
        return $this->uploadType;
    }

    public function setTarget(string $target): void
    {
        $this->target = sprintf($target . '/' . $this->targetFileName, $this->uploadType);
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if ($key === 'productId') {
            $this->setUploadType(self::PRODUCT_TYPE);
        }
        if ($key === 'categoryId') {
            $this->setUploadType(self::CATEGORY_TYPE);
        }
        if ($this->flag === self::ARRAY_AS_PROPS) {
            $this->offsetSet($key, $value);
            return;
        }
        if (in_array($key, $this->protectedProperties)) {
            throw new Exception\InvalidArgumentException("$key is a protected property, use a different key");
        }
        $this->$key = $value;
    }
}
