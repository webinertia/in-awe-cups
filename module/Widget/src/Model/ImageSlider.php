<?php

declare(strict_types=1);

namespace Widget\Model;

use App\Model\ModelInterface;
use Laminas\Config\Factory;

use function count;

class ImageSlider implements ModelInterface
{
    public const CONFIG_FILE = __DIR__ . '/../../config/slider.config.php';

    /** @var array<mixed> $data */
    public $data;
    /** @var array<mixed> $config */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->data   = Factory::fromFile(self::CONFIG_FILE)['slide_data'];
    }

    public function getSlideCount(): int
    {
        return count($this->data);
    }

    public function getOwnerId(): mixed
    {
        return null;
    }

    public function getResourceId(): string
    {
        return 'imageslider';
    }
}
