<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function compressAndStore(UploadedFile $file, string $directory, string $disk = 'public', int $quality = 60): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return $file->store($directory, $disk);
        }

        $image = $extension === 'png'
            ? @imagecreatefrompng($file->getRealPath())
            : @imagecreatefromjpeg($file->getRealPath());

        if (!$image) {
            return $file->store($directory, $disk);
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $storedName = $filename . '_' . time() . '_' . uniqid() . '.jpg';
        $tempPath = sys_get_temp_dir() . '/' . $storedName;

        imagejpeg($image, $tempPath, $quality);
        imagedestroy($image);

        $storedPath = Storage::disk($disk)->putFileAs($directory, new \Illuminate\Http\UploadedFile($tempPath, $storedName), $storedName);

        @unlink($tempPath);

        return $storedPath;
    }
}
