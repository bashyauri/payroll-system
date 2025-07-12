<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => strtoupper('STF-' . fake()->bothify('##??' . rand(100, 999))),
            'name' => fake('en_NG')->name(), // Use Nigerian locale
            'department_id' => Department::factory(),
            'level' => 'Level ' . fake()->numberBetween(1, 10),
            'step' => 'Step ' . fake()->numberBetween(1, 5),
            'basic_salary' => fake()->numberBetween(50000, 200000),
            'bank_name' => fake()->randomElement(['UBA', 'GTBank', 'Access Bank']),
            'account_number' => fake()->bankAccountNumber(),
        ];
    }
}