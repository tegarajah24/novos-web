<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return view('internal.kelola-produk');
    }
}
