<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;

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
            Setting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan',
            'data'    => $data,
        ]);
    }
}
