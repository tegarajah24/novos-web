<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Dashboard',          'slug' => 'dashboard'],
            ['name' => 'Summary',            'slug' => 'summary'],
            ['name' => 'Daftar Pesanan',     'slug' => 'orders'],
            ['name' => 'Design',             'slug' => 'design'],
            ['name' => 'Produksi',           'slug' => 'production'],
            ['name' => 'Daily Mental Check', 'slug' => 'daily-mental-check'],
            ['name' => 'Laporan',            'slug' => 'reports'],
            ['name' => 'Kelola Produk',      'slug' => 'manage-products'],
            ['name' => 'Kategori',           'slug' => 'categories'],
            ['name' => 'Kelola Pengguna',    'slug' => 'manage-users'],
            ['name' => 'Pengaturan',         'slug' => 'settings'],
        ];

        $roleMap = [
            'Super Admin' => 'Super Admin',
            'Manager'     => 'Manager',
            'Admin'       => 'Admin',
            'Produksi'    => 'Tim Produksi',
            'Design'      => 'Tim Design',
        ];

        $accessMap = [
            'ada'        => 'full',
            'lihat saja' => 'view',
            'tidak ada'  => 'none',
        ];

        $csvData = [
            'Super Admin' => ['ada','ada','ada','ada','ada','ada','ada','ada','ada','ada','ada'],
            'Manager'     => ['ada','ada','ada','lihat saja','lihat saja','ada','ada','tidak ada','tidak ada','tidak ada','tidak ada'],
            'Admin'       => ['ada','tidak ada','ada','ada','ada','ada','tidak ada','ada','ada','tidak ada','tidak ada'],
            'Produksi'    => ['ada','tidak ada','lihat saja','tidak ada','ada','ada','tidak ada','tidak ada','tidak ada','tidak ada','tidak ada'],
            'Design'      => ['ada','tidak ada','lihat saja','ada','tidak ada','ada','tidak ada','tidak ada','tidak ada','tidak ada','tidak ada'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['slug' => $p['slug']],
                ['name' => $p['name'], 'description' => $p['name']]
            );
        }

        $roles = Role::whereIn('name', array_keys($roleMap))->get()->keyBy('name');
        $allPermissions = Permission::all()->keyBy('slug');

        $permissionSlugs = array_column($permissions, 'slug');

        foreach ($csvData as $roleName => $accessLevels) {
            $role = $roles->get($roleName);
            if (!$role) continue;

            $attachData = [];
            foreach ($permissionSlugs as $i => $slug) {
                $perm = $allPermissions->get($slug);
                if (!$perm) continue;

                $csvLevel = $accessLevels[$i] ?? 'tidak ada';
                $level = $accessMap[$csvLevel] ?? 'none';

                $attachData[$perm->id] = ['access_level' => $level];
            }

            $role->permissions()->sync($attachData);
        }
    }
}
