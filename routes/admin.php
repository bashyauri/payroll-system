<?php

use App\Livewire\Admin\Employees\Allowance;
use App\Livewire\Admin\Employees\Deductions;
use App\Livewire\Admin\Employees\ManageEmployees;
use App\Livewire\Admin\Users\ManageUsers;
use App\Livewire\Payroll\GeneratePayslip;
use App\Livewire\Payroll\Salaries;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', ManageUsers::class)->name('admin.users');
    Route::get('/admin/employees', ManageEmployees::class)->name('admin.employees');
    Route::get('admin/employees/deductions', Deductions::class)->name('admin.employees.deductions');
    Route::get('admin/employees/allowances', Allowance::class)->name('admin.employees.allowances');
    // Route::get('hr/employees/generate-slip', GeneratePayslip::class)->name('hr.employees.generate-slip');
    // Route::get('hr/employees/salaries', Salaries::class)->name('hr.employees.salaries');
});
