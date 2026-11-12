<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Format;

class ImageService
{
    public function store(UploadedFile $file, string $folder, int $maxWidth = 1200): string
    {
        $filename = uniqid() . '.jpg';
        $path = $folder . '/' . $filename;

        $image = Image::decode($file)
            ->scaleDown(width: $maxWidth)
            ->encodeUsingFormat(Format::JPEG, quality: 80);

        Storage::disk('public')->put($path, $image);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
