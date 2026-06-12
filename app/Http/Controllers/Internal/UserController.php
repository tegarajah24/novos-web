<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', ['Manager', 'Admin', 'Design', 'Produksi']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'username'   => explode('@', $user->email)[0],
                    'phone'      => $user->phone ?? '-',
                    'role'       => $user->role->name,
                    'status'     => 'Aktif',
                    'created_at' => $user->created_at->format('d M Y'),
                ];
            })
            ->values()
            ->toArray();

        return view('internal.kelola-pengguna', compact('users'));
    }
}
