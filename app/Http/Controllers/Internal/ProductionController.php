<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class ProductionController extends Controller
{
    public function index()
    {
        return view('internal.produksi');
    }
}
