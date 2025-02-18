<?php

namespace App\Service;

use Illuminate\Http\UploadedFile;

interface ImageUploaderInterface
{
    public function uploadImage(string $fileName, UploadedFile $uploadedFile);
}
