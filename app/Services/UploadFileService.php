<?php
declare(strict_types=1);

namespace App\Services;

use League\Flysystem\Filesystem;
use Psr\Http\Message\UploadedFileInterface;

class UploadFileService
{
    public function __construct(private readonly Filesystem $filesystem) {

    }

    public function upload(array $data, string $uploadName, string $path): array
    {
        /** @var UploadedFileInterface $fileData */
        $fileData = $data[$uploadName] ?? null;

        $filename = $fileData->getClientFilename();

        $extension = pathinfo($fileData->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));

        $pathName = sprintf('%s.%0.8s', $basename, $extension);
        
        // 2 Store the data
        $this->filesystem->write( $path . $pathName, $fileData->getStream()->getContents());

        return [
            'filename' => $filename,
            'pathName' => $pathName,
        ];
    }
}