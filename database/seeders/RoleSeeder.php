<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@antrian.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'phone' => '+62812345678',
            'company' => 'Antrian Project',
            'bio' => 'System Super Administrator',
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@antrian.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+62812345679',
            'company' => 'Antrian Project',
            'bio' => 'System Administrator',
        ]);

        // Create Developers
        User::create([
            'name' => 'John Developer',
            'email' => 'developer1@antrian.com',
            'password' => Hash::make('password'),
            'role' => 'developer',
            'status' => 'active',
            'phone' => '+62812345680',
            'company' => 'Antrian Project',
            'bio' => 'Full Stack Developer',
        ]);

        User::create([
            'name' => 'Jane Developer',
            'email' => 'developer2@antrian.com',
            'password' => Hash::make('password'),
            'role' => 'developer',
            'status' => 'active',
            'phone' => '+62812345681',
            'company' => 'Antrian Project',
            'bio' => 'Frontend Developer',
        ]);

        // Create Clients
        User::create([
            'name' => 'Client One',
            'email' => 'client1@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'phone' => '+62812345682',
            'company' => 'ABC Company',
            'bio' => 'CEO of ABC Company',
        ]);

        User::create([
            'name' => 'Client Two',
            'email' => 'client2@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'phone' => '+62812345683',
            'company' => 'XYZ Corporation',
            'bio' => 'Project Manager at XYZ',
        ]);

        User::create([
            'name' => 'Client Three',
            'email' => 'client3@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'phone' => '+62812345684',
            'company' => 'Tech Startup',
            'bio' => 'Founder of Tech Startup',
        ]);
    }
}
