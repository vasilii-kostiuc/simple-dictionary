<?php

namespace App\Service;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploader implements ImageUploaderInterface
{
    public function uploadImage(string $fileName, UploadedFile $uploadedFile)
    {
        Storage::disk('public')->putFileAs("", $uploadedFile,$fileName);
    }
}
