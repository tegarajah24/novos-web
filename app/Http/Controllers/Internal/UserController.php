<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|string|in:Manager,Admin,Design,Produksi',
        ]);

        $role = Role::where('name', $data['role'])->firstOrFail();

        $user = DB::transaction(function () use ($data, $role) {
            return User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => $role->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'username'   => explode('@', $user->email)[0],
                'phone'      => $user->phone ?? '-',
                'role'       => $role->name,
                'status'     => 'Aktif',
                'created_at' => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|string|in:Manager,Admin,Design,Produksi',
        ]);

        $role = Role::where('name', $data['role'])->firstOrFail();

        DB::transaction(function () use ($data, $role, $user) {
            $user->update([
                'name'    => $data['name'],
                'email'   => $data['email'],
                'role_id' => $role->id,
            ]);

            if ($data['password']) {
                $user->update(['password' => Hash::make($data['password'])]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'username'   => explode('@', $user->email)[0],
                'phone'      => $user->phone ?? '-',
                'role'       => $role->name,
                'status'     => 'Aktif',
                'created_at' => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus',
        ]);
    }
}
