<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AllowanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Peculiar',
            'Accommodation',
            'Transport',
            'Hazard',
            'Medical',
        ];

        foreach ($types as $type) {
            DB::table('allowance_types')->updateOrInsert(['name' => $type]);
        }
    }
}
