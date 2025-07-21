<?php

use App\Livewire\Payroll\GeneratePayslip;
use App\Livewire\Payroll\Salaries;

use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:hr'])->group(function () {
    Route::get('hr/employees/generate-slip', GeneratePayslip::class)->name('hr.employees.generate-slip');
    Route::get('hr/employees/salaries', Salaries::class)->name('hr.employees.salaries');
});
