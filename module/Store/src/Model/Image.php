<?php

declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelTrait;
use App\Upload\UploadAwareModelInterface;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Store\Db\TableGateway\ImageTable;
use User\Service\UserService;

use function count;
use function sprintf;

final class Image extends AbstractGatewayModel implements UploadAwareModelInterface
{
    use ModelTrait;

    public const IMAGE_TARGET_PATH = __DIR__ . '/../../../../public/modules/store/%s/images';
    public const CATEGORY_TYPE     = 'category';
    public const PRODUCT_TYPE      = 'product';

    /** @var BaseName $baseName */
    protected $baseName;
    /** @var RenameUpload $renameUpload */
    protected $renameUpload;
    /** @var null|string $uploadType */
    protected $uploadType;
    /** @var string $targetFileName */
    protected $targetFileName = '%sImage';
    /** @var string $target */
    protected $target;
    /** @var array<int, string> */
    protected $supportedKeyNames = [
        'image',
        'imageFile',
        'image-file',
        'file',
        'images',
        'imageFiles',
        'image-files',
        'files',
    ];
    /** @var array<int, array> $persistedData */
    protected $persistedData = [];

    public function __construct(UserService $userService, RenameUpload $renameUpload, BaseName $baseName, ?ImageTable $imageTable = null)
    {
        parent::__construct([]);
        $this->userId       = $userService->id;
        $this->renameUpload = $renameUpload;
        $this->baseName     = $baseName;
        if ($imageTable !== null) {
            $this->gateway = $imageTable;
        }
    }

    public function handleUpload(array $fileData)
    {
        $fileCount = count($fileData);
        for ($i = 0; $i < $fileCount; $i++) {
            $renamed     = $this->renameUpload->filter($fileData[$i]);
            $this->fileName = $this->baseName->filter($renamed['tmp_name']);
            $this->save($this);
        }
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
}
