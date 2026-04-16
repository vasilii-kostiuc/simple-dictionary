<?php

namespace App\Infrastructure\Uploads;

use Illuminate\Http\UploadedFile;

interface ImageUploaderInterface
{
    public function uploadImage(string $fileName, UploadedFile $uploadedFile): void;
}
