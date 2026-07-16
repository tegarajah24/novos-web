<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allAsArray();

        return view('internal.pengaturan', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $data = $request->validated();

        foreach ($data as $key => $value) {
            Setting::set($key, is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan',
            'data'    => $data,
        ]);
    }

    public function uploadHero(Request $request)
    {
        $request->validate([
            'file'   => 'required|file|image|mimes:jpeg,jpg,png,webp|max:5120',
            'target' => 'required|string|in:beranda,tentang,katalog',
        ]);

        $file = $request->file('file');
        $target = $request->input('target');

        $settingKey = "hero_{$target}_bg";

        $oldValue = Setting::get($settingKey);

        $dir = 'hero-backgrounds';
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $filename = $target . '-' . time() . '-' . uniqid() . '.jpg';
        $tempPath = storage_path("app/public/{$dir}/{$filename}");

        $img = @imagecreatefromjpeg($file->getPathname())
            ?? @imagecreatefrompng($file->getPathname())
            ?? @imagecreatefromwebp($file->getPathname())
            ?? @imagecreatefromstring(file_get_contents($file->getPathname()));

        if (!$img) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses gambar.',
            ], 422);
        }

        $maxWidth = 1920;
        $width = imagesx($img);
        $height = imagesy($img);

        if ($width > $maxWidth) {
            $newHeight = (int) ($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($maxWidth, $newHeight);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($img);
            $img = $resized;
        }

        $result = imagejpeg($img, $tempPath, 80);
        imagedestroy($img);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan gambar.',
            ], 500);
        }

        if ($oldValue && Storage::disk('public')->exists("{$dir}/{$oldValue}")) {
            Storage::disk('public')->delete("{$dir}/{$oldValue}");
        }

        Setting::set($settingKey, $filename);

        return response()->json([
            'success' => true,
            'message' => 'Background hero berhasil diunggah.',
            'url'     => Storage::disk('public')->url("{$dir}/{$filename}"),
            'key'     => $settingKey,
            'value'   => $filename,
        ]);
    }
}
