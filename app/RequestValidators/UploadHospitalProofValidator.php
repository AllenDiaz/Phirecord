<?php

declare(strict_types = 1);

namespace App\RequestValidators;

use App\Exception\ValidationException;
use Psr\Http\Message\UploadedFileInterface;
use App\Contracts\RequestValidatorInterface;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

class UploadHospitalProofValidator implements RequestValidatorInterface
{
    public function validate(array $data, string $file = null): array
    {
        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $data[$file] ?? null;

        // 1. Validate uploaded file
        if (! $uploadedFile) {
            throw new ValidationException([$file => ['Please select a image file']]);
        }

        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new ValidationException([$file => ['Failed to upload the image file']]);
        }

        // 2. Validate the file size
        $maxFileSize = 5 * 1024 * 1024;

        if ($uploadedFile->getSize() > $maxFileSize) {
            throw new ValidationException([$file => ['Maximum allowed size is 5 MB']]);
        }

        // 3. Validate the file name
        $filename = $uploadedFile->getClientFilename();

        if (! preg_match('/^[a-zA-Z0-9\s._-]+$/', $filename)) {
            throw new ValidationException([$file => ['Invalid filename']]);
        }

        // 4. Validate file type
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        $tmpFilePath      = $uploadedFile->getStream()->getMetadata('uri');

        if (! in_array($uploadedFile->getClientMediaType(), $allowedMimeTypes)) {
            throw new ValidationException([$file => ['has to be image only']]);
        }

        $detector = new FinfoMimeTypeDetector();
        $mimeType = $detector->detectMimeTypeFromFile($tmpFilePath);
        
        if (! in_array($mimeType, $allowedMimeTypes)) {
            throw new ValidationException([$file => ['Invalid file type']]);
        }

        return $data;
    }
}
