<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'hr'],
            ['name' => 'staff'],
        ]);
    }
    public function down(): void
    {
        DB::table('roles')->truncate();
    }
}
