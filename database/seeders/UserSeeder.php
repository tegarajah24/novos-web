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

        User::firstOrCreate(
            ['email' => 'customer@novos.com'],
            [
                'name' => 'Customer Test',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Customer')->first()->id,
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

        User::firstOrCreate(
            ['email' => 'testing@novos.com'],
            [
                'name' => 'Testing Customer',
                'password' => bcrypt('password'),
                'role_id' => Role::where('name', 'Customer')->first()->id,
            ]
        );
    }
}
