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
        User::updateOrCreate(
            ['email' => 'superadmin@antrian.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'active',
                'phone' => '+62812345678',
                'company' => 'Antrian Project',
                'bio' => 'System Super Administrator',
            ]
        );

        // Create Operational Manager
        User::updateOrCreate(
            ['email' => 'om@antrian.com'],
            [
                'name' => 'Operational Manager',
                'password' => Hash::make('password'),
                'role' => 'operational_manager',
                'status' => 'active',
                'phone' => '+62812345671',
                'company' => 'Antrian Project',
                'bio' => 'Operational Division Manager',
            ]
        );

        // Create General Manager
        User::updateOrCreate(
            ['email' => 'gm@antrian.com'],
            [
                'name' => 'General Manager',
                'password' => Hash::make('password'),
                'role' => 'general_manager',
                'status' => 'active',
                'phone' => '+62812345672',
                'company' => 'Antrian Project',
                'bio' => 'General Management Head',
            ]
        );

        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@antrian.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+62812345679',
                'company' => 'Antrian Project',
                'bio' => 'System Administrator',
            ]
        );

        // Create Developers
        User::updateOrCreate(
            ['email' => 'developer1@antrian.com'],
            [
                'name' => 'John Developer',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'status' => 'active',
                'phone' => '+62812345680',
                'company' => 'Antrian Project',
                'bio' => 'Full Stack Developer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'developer2@antrian.com'],
            [
                'name' => 'Jane Developer',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'status' => 'active',
                'phone' => '+62812345681',
                'company' => 'Antrian Project',
                'bio' => 'Frontend Developer',
            ]
        );

        // Create Clients
        User::updateOrCreate(
            ['email' => 'client1@example.com'],
            [
                'name' => 'Client One',
                'password' => Hash::make('password'),
                'role' => 'client',
                'status' => 'active',
                'phone' => '+62812345682',
                'company' => 'ABC Company',
                'bio' => 'CEO of ABC Company',
            ]
        );

        User::updateOrCreate(
            ['email' => 'client2@example.com'],
            [
                'name' => 'Client Two',
                'password' => Hash::make('password'),
                'role' => 'client',
                'status' => 'active',
                'phone' => '+62812345683',
                'company' => 'XYZ Corporation',
                'bio' => 'Project Manager at XYZ',
            ]
        );

        User::updateOrCreate(
            ['email' => 'client3@example.com'],
            [
                'name' => 'Client Three',
                'password' => Hash::make('password'),
                'role' => 'client',
                'status' => 'active',
                'phone' => '+62812345684',
                'company' => 'Tech Startup',
                'bio' => 'Founder of Tech Startup',
            ]
        );
    }
}
