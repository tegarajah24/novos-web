<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allAsArray();

        return view('internal.pengaturan', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name'    => 'required|string|max:255',
            'company_phone'   => 'nullable|string|max:50',
            'company_email'   => 'nullable|email|max:255',
            'company_address' => 'nullable|string|max:1000',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan',
        ]);
    }
}
