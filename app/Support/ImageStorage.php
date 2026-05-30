<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageStorage
{
    public static function storeAsWebp(UploadedFile $file, string $directory): string
    {
        $image = match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($file->getPathname()),
            'image/png' => imagecreatefrompng($file->getPathname()),
            'image/gif' => imagecreatefromgif($file->getPathname()),
            default => null,
        };

        if (! $image) {
            throw new RuntimeException('Format gambar tidak didukung: ' . $file->getMimeType());
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $filename = Str::uuid() . '.webp';
        $path = "{$directory}/{$filename}";
        $fullPath = Storage::disk('public')->path($path);

        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        imagewebp($image, $fullPath, 80);
        // imagedestroy($image);

        return $path;
    }
}