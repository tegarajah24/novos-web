<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KelolaPengguna extends Component
{
    use WithFileUploads;

    public $search = '';
    public $roleFilter = '';
    public $showModal = false;
    public $editingUserId = null;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';
    public $avatar;
    public $existingAvatar = null;

    public $submitting = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->editingUserId ?? 'NULL'),
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];

        if (!$this->editingUserId) {
            $rules['password'] = 'required|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|min:8|confirmed';
        }

        return $rules;
    }

    protected function getListeners()
    {
        return ['notify'];
    }

    public function getRolesProperty()
    {
        return Role::whereIn('name', Role::internalNames())->pluck('name')->toArray();
    }

    public function getUsersProperty()
    {
        $query = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', Role::internalNames()))
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            }))
            ->when($this->roleFilter, fn($q) => $q->whereHas('role', fn($r) => $r->where('name', $this->roleFilter)))
            ->orderBy('created_at', 'desc');

        $users = $query->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => explode('@', $user->email)[0],
                'role' => $user->role->name,
                'avatar' => $user->avatar,
                'status' => 'Aktif',
                'created_at' => $user->created_at->format('d M Y'),
            ];
        })->values()->toArray();

        $this->totalUsers = count($users);
        $this->totalManager = count(array_filter($users, fn($u) => $u['role'] === 'Manager'));
        $this->totalAdmin = count(array_filter($users, fn($u) => $u['role'] === 'Admin'));
        $this->totalProduksiDesign = count(array_filter($users, fn($u) => in_array($u['role'], ['Produksi', 'Design'])));

        return $users;
    }

    public function openCreate()
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->avatar = null;
        $this->existingAvatar = null;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $user = User::with('role')->findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = $user->role->name;
        $this->avatar = null;
        $this->existingAvatar = $user->avatar ? asset('storage/' . $user->avatar) : null;
        $this->showModal = true;
    }

    public function closeForm()
    {
        $this->showModal = false;
        $this->editingUserId = null;
    }

    public function save()
    {
        $this->validate();

        $this->submitting = true;

        $roleModel = Role::where('name', $this->role)->firstOrFail();

        $avatarPath = null;

        if ($this->avatar) {
            $filename = 'avatar_' . time() . '_' . uniqid() . '.jpg';
            $destinationPath = storage_path('app/public/avatars/' . $filename);

            if (!file_exists(storage_path('app/public/avatars'))) {
                mkdir(storage_path('app/public/avatars'), 0755, true);
            }

            $tmpPath = $this->avatar->getRealPath();
            $ext = strtolower($this->avatar->getClientOriginalExtension());
            $image = $ext === 'png' ? @imagecreatefrompng($tmpPath) : @imagecreatefromjpeg($tmpPath);

            if ($image) {
                imagejpeg($image, $destinationPath, 60);
                imagedestroy($image);
                $avatarPath = 'avatars/' . $filename;
            }
        }

        DB::transaction(function () use ($roleModel, $avatarPath) {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role_id' => $roleModel->id,
            ];

            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            }

            if ($this->editingUserId) {
                $user = User::findOrFail($this->editingUserId);
                if ($avatarPath && $user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->update($data);
                if ($this->password) {
                    $user->update(['password' => Hash::make($this->password)]);
                }
            } else {
                $data['password'] = Hash::make($this->password);
                User::create($data);
            }
        });

        $this->submitting = false;
        $this->closeForm();

        $this->dispatch('notify', type: 'success', message: $this->editingUserId ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();
        $this->dispatch('notify', type: 'success', message: 'Pengguna berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.kelola-pengguna');
    }
}
