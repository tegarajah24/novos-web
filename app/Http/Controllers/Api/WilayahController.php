<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    public function provinces(): JsonResponse
    {
        $data = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')
            ->orderBy('nama')
            ->get()
            ->map(fn($item) => ['id' => $item->kode, 'name' => $item->nama]);

        return response()->json($data);
    }

    public function regencies(string $provinceCode): JsonResponse
    {
        $data = Wilayah::where('kode', 'LIKE', "$provinceCode.%")
            ->whereRaw('CHAR_LENGTH(kode) = 5')
            ->orderBy('nama')
            ->get()
            ->map(fn($item) => ['id' => $item->kode, 'name' => $item->nama]);

        return response()->json($data);
    }

    public function districts(string $regencyCode): JsonResponse
    {
        $data = Wilayah::where('kode', 'LIKE', "$regencyCode.%")
            ->whereRaw('CHAR_LENGTH(kode) = 8')
            ->orderBy('nama')
            ->get()
            ->map(fn($item) => ['id' => $item->kode, 'name' => $item->nama]);

        return response()->json($data);
    }
}
