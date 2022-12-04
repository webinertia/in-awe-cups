<?php

declare(strict_types=1);

namespace App\Upload;

interface UploadAwareModelInterface
{
    public function handleUpload(array $fileData);
}
