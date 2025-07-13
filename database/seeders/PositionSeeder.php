<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['title' => 'Rector', 'base_salary' => 450000],
            ['title' => 'Deputy Rector', 'base_salary' => 400000],
            ['title' => 'Registrar', 'base_salary' => 380000],
            ['title' => 'Bursar', 'base_salary' => 370000],
            ['title' => 'Librarian', 'base_salary' => 360000],
            ['title' => 'Dean of School', 'base_salary' => 350000],
            ['title' => 'Chief Lecturer', 'base_salary' => 400000], // ✅ Replaces HOD
            ['title' => 'Lecturer I', 'base_salary' => 300000],
            ['title' => 'Lecturer II', 'base_salary' => 250000],
            ['title' => 'Assistant Lecturer', 'base_salary' => 200000],
            ['title' => 'Technologist', 'base_salary' => 180000],
            ['title' => 'System Analyst', 'base_salary' => 220000],
            ['title' => 'Administrative Officer', 'base_salary' => 160000],
            ['title' => 'Clerical Staff', 'base_salary' => 120000],
            ['title' => 'Security Personnel', 'base_salary' => 90000],
            ['title' => 'Cleaner', 'base_salary' => 80000],
        ];

        foreach ($positions as $position) {
            DB::table('positions')->updateOrInsert(
                ['title' => $position['title']],
                [
                    'base_salary' => $position['base_salary'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
