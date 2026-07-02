<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('internal.kelola-pengguna');
    }
}
