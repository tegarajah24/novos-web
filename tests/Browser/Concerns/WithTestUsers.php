<?php

namespace Tests\Browser\Concerns;

use App\Models\Role;
use App\Models\User;

trait WithTestUsers
{
    protected function ensureRolesAndUsersExist(): void
    {
        $roles = ['Super Admin', 'Manager', 'Admin', 'Design', 'Produksi', 'Customer'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        User::firstOrCreate(
            ['email' => 'superadmin@novos.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Super Admin')->first()->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@novos.com'],
            [
                'name' => 'Admin Novos',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Admin')->first()->id,
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@novos.com'],
            [
                'name' => 'Customer Test',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Customer')->first()->id,
                'phone' => '081234567890',
            ]
        );
        if (empty($customer->phone)) {
            $customer->update(['phone' => '081234567890']);
        }

        \App\Models\CustomerAddress::firstOrCreate(
            ['user_id' => $customer->id],
            [
                'first_name' => 'Customer',
                'last_name' => 'Test',
                'province' => '32', // Jawa Barat province code
                'city' => '3273', // Bandung city code
                'district' => '3273010', // District code
                'detail_address' => 'Jl. Coblong Raya No. 10',
                'postal_code' => '40135',
                'address_type' => 'rumah',
                'is_primary' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'design@novos.com'],
            [
                'name' => 'Tim Design',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Design')->first()->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'produksi@novos.com'],
            [
                'name' => 'Tim Produksi',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Produksi')->first()->id,
            ]
        );
    }
}
