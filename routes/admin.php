<?php

use App\Livewire\Admin\Employees\ManageEmployees;
use App\Livewire\Admin\Users\ManageUsers;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', ManageUsers::class)->name('admin.users');
    Route::get('/admin/employees', ManageEmployees::class)->name('admin.employees');
});
