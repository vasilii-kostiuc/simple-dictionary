<?php

namespace App\Infrastructure\Uploads;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploader implements ImageUploaderInterface
{
    public function uploadImage(string $fileName, UploadedFile $uploadedFile): void
    {
        Storage::disk('public')->putFileAs('', $uploadedFile, $fileName);
    }
}
