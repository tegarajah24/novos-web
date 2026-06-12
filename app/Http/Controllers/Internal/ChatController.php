<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index()
    {
        return view('internal.chat');
    }
}
