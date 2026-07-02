<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class DesignController extends Controller
{
    public function index()
    {
        return view('internal.design');
    }
}
