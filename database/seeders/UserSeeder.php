<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@novos.com',
            'password' => bcrypt('password'),
            'role_id'  => Role::where('name', 'Super Admin')->first()->id,
        ]);

        User::create([
            'name'     => 'Admin Novos',
            'email'    => 'admin@novos.com',
            'password' => bcrypt('password'),
            'role_id'  => Role::where('name', 'Admin')->first()->id,
        ]);

        User::create([
            'name'     => 'Customer Test',
            'email'    => 'customer@novos.com',
            'password' => bcrypt('password'),
            'role_id'  => Role::where('name', 'Customer')->first()->id,
        ]);

        User::create([
            'name'     => 'Tim Design',
            'email'    => 'design@novos.com',
            'password' => bcrypt('password'),
            'role_id'  => Role::where('name', 'Design')->first()->id,
        ]);

        User::create([
            'name'     => 'Tim Produksi',
            'email'    => 'produksi@novos.com',
            'password' => bcrypt('password'),
            'role_id'  => Role::where('name', 'Produksi')->first()->id,
        ]);
    }
}
