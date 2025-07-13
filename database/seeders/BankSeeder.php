<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $banks = [
            'Access Bank',
            'Citibank Nigeria',
            'Ecobank Nigeria',
            'Fidelity Bank',
            'First Bank of Nigeria',
            'First City Monument Bank (FCMB)',
            'Globus Bank',
            'Guaranty Trust Bank (GTBank)',
            'Heritage Bank',
            'Keystone Bank',
            'Polaris Bank',
            'Providus Bank',
            'Stanbic IBTC Bank',
            'Standard Chartered Bank',
            'Sterling Bank',
            'SunTrust Bank',
            'Titan Trust Bank',
            'Union Bank of Nigeria',
            'United Bank for Africa (UBA)',
            'Unity Bank',
            'Wema Bank',
            'Zenith Bank',
        ];

        foreach ($banks as $name) {
            DB::table('banks')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
