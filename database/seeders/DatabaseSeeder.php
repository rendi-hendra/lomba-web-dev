<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Role Data Awal sesuai SQL
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'petugas']);
        Role::firstOrCreate(['name' => 'warga']);

        // Default Admin User
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'id_role' => $adminRole->id_role,
                'name' => 'Administrator',
                'password' => Hash::make('admin'),
            ]
        );
    }
}
