<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', Role::internalNames()))
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($user) {
                return [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'username'   => explode('@', $user->email)[0],
                    'public_title' => $user->public_title,
                    'role'       => $user->role->name,
                    'avatar'     => $user->avatar,
                    'status'     => $user->is_active ? 'Aktif' : 'Nonaktif',
                    'created_at' => $user->created_at->format('d M Y'),
                    'sort_order' => $user->sort_order ?? 0,
                ];
            })
            ->values()
            ->toArray();

        return view('internal.kelola-pengguna', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $role = Role::where('name', $data['role'])->firstOrFail();

        if (auth()->user()->role->name !== 'Super Admin' && $role->name === 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuat akun Super Admin',
            ], 403);
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . time() . '_' . uniqid() . '.jpg';
            $destinationPath = storage_path('app/public/avatars/' . $filename);

            if (!file_exists(storage_path('app/public/avatars'))) {
                mkdir(storage_path('app/public/avatars'), 0755, true);
            }

            $image = strtolower($file->getClientOriginalExtension()) === 'png'
                ? @imagecreatefrompng($file->getRealPath())
                : @imagecreatefromjpeg($file->getRealPath());

            if ($image) {
                imagejpeg($image, $destinationPath, 60);
                imagedestroy($image);
            }

            $avatarPath = 'avatars/' . $filename;
        }

        $isActive = ($data['status'] ?? 'Aktif') === 'Aktif';

        $user = DB::transaction(function () use ($data, $role, $avatarPath, $isActive) {
            return User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'role_id'   => $role->id,
                'public_title' => $data['public_title'] ?? null,
                'avatar'    => $avatarPath,
                'is_active' => $isActive,
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
                'public_title' => $user->public_title,
                'role'       => $role->name,
                'avatar'     => $user->avatar,
                'status'     => $isActive ? 'Aktif' : 'Nonaktif',
                'created_at' => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $role = Role::where('name', $data['role'])->firstOrFail();

        if (auth()->user()->role->name !== 'Super Admin' && $role->name === 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menetapkan role Super Admin',
            ], 403);
        }

        if (auth()->user()->role->name !== 'Super Admin' && $user->role->name === 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah data Super Admin',
            ], 403);
        }

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
            $destinationPath = storage_path('app/public/avatars/' . $filename);

            if (!file_exists(storage_path('app/public/avatars'))) {
                mkdir(storage_path('app/public/avatars'), 0755, true);
            }

            $image = strtolower($file->getClientOriginalExtension()) === 'png'
                ? @imagecreatefrompng($file->getRealPath())
                : @imagecreatefromjpeg($file->getRealPath());

            if ($image) {
                imagejpeg($image, $destinationPath, 60);
                imagedestroy($image);
            }

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = 'avatars/' . $filename;
        }

        $isActive = ($data['status'] ?? 'Aktif') === 'Aktif';

        DB::transaction(function () use ($data, $role, $user, $avatarPath, $isActive) {
            $user->update([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'public_title' => $data['public_title'] ?? null,
                'role_id'   => $role->id,
                'avatar'    => $avatarPath,
                'is_active' => $isActive,
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
                'public_title' => $user->public_title,
                'role'       => $role->name,
                'avatar'     => $user->fresh()->avatar,
                'status'     => $isActive ? 'Aktif' : 'Nonaktif',
                'created_at' => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri',
            ], 400);
        }

        if ($user->role->name === 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin tidak dapat dihapus',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus',
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:users,id',
        ]);

        foreach ($request->order as $index => $id) {
            User::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan pengguna berhasil diperbarui',
        ]);
    }
}
