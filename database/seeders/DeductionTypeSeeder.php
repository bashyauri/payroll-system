<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DeductionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'SSNAIP Loan',
            'Ramadan Loan',
            'PAYE',
            'Housing',
            'Water Rate',
            'Union Due'
        ];

        foreach ($types as $type) {
            DB::table('deduction_types')->updateOrInsert(['name' => $type]);
        }
    }
}
