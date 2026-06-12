<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('internal.dashboard');
    }
}
