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
                    'avatar'     => $user->avatar,
                    'status'     => 'Aktif',
                    'created_at' => $user->created_at->format('d M Y'),
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

        $user = DB::transaction(function () use ($data, $role, $avatarPath) {
            return User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => $role->id,
                'avatar'   => $avatarPath,
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
                'avatar'     => $user->avatar,
                'status'     => 'Aktif',
                'created_at' => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $role = Role::where('name', $data['role'])->firstOrFail();

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

        DB::transaction(function () use ($data, $role, $user, $avatarPath) {
            $user->update([
                'name'    => $data['name'],
                'email'   => $data['email'],
                'role_id' => $role->id,
                'avatar'  => $avatarPath,
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
                'avatar'     => $user->fresh()->avatar,
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
