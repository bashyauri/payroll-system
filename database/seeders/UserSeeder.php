<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);
        $admin->roles()->attach(Role::where('name', 'admin')->first());

        // HR
        $hr = User::create([
            'name' => 'HR User',
            'email' => 'hr@example.com',
            'password' => Hash::make('password')
        ]);
        $hr->roles()->attach(Role::where('name', 'hr')->first());

        // Staff
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password')
        ]);
        $staff->roles()->attach(Role::where('name', 'staff')->first());
    }
}