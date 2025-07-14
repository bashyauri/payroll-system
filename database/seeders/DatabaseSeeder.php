<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Department;
use App\Models\Employee;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{


    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,
            BankSeeder::class,
            PositionSeeder::class,
            DeductionTypeSeeder::class,
        ]);

        // Seed 10 Nigerian-style staff users and employees
        // User::factory(10)->create()->each(function ($user) {
        //     $staffRole = Role::where('name', 'staff')->first();
        //     $user->roles()->attach($staffRole);

        //     Employee::factory()->create([
        //         'user_id' => $user->id,
        //         'name' => $user->name,
        //     ]);
        // });
    }
}
