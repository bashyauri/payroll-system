<?php

namespace App\Livewire\Admin\Employees;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;

class ManageEmployees extends Component
{
    // Employee Fields
    public $staff_id;
    public $name;
    public $email;
    public $phone;
    public $department_id;
    public $position_id;
    public $level;
    public $step;
    public $bank_name;
    public $account_number;
    public $hire_date;
    public $departments = [];
    public $userSearch = '';
    public $selectedUser = null;
    public $users = [];
    public string $sortBy = 'name'; // default sorting field
    public string $sortDirection = 'asc';
    public $search = '';
    public $positions = [];

    public function rules()
    {
        return [
            'staff_id' => 'required|string|unique:employees,staff_id',
            'user_id' => 'required|string|unique:users,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'level' => 'nullable|string|max:10',
            'step' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
        ];
    }
    public function mount()
    {
        $this->departments = Department::all();
        $this->positions = Position::all();
        $this->generateStaffId();
    }
    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) > 2) {
            $this->users = User::query()
                ->where('name', 'like', '%' . $this->userSearch . '%')
                ->orWhere('email', 'like', '%' . $this->userSearch . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->users = [];
        }
    }
    public function selectUser($user)
    {
        $this->selectedUser = $user;
        $this->userSearch = '';
        $this->users = [];
    }

    public function resetUserSelection()
    {
        $this->selectedUser = null;
        $this->userSearch = '';
        $this->users = [];
    }
    public function updatingSearch()
    {

        $this->resetPage();
    }
    #[\Livewire\Attributes\Computed]
    public function employees()
    {

        return Employee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('roles', function ($roleQuery) {
                            $roleQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('department', function ($deptQuery) {
                            $deptQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->with(['roles', 'department'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
    public function saveEmployee()
    {
        $validated = $this->validate();
        try {
            $employee = Employee::create([
                'staff_id' => $validated['staff_id'],
                'user_id' => $validated['staff_id'],
                'department_id' => $validated['department_id'],
                'position_id' => $validated['position_id'],
                'level' => $validated['level'],
                'step' => $validated['step'],
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'hire_date' => $validated['hire_date'] ? Carbon::parse($validated['hire_date']) : null,
                'phone_number' => $validated['phone'],
            ]);

            $this->dispatch('employee-added', message: 'Employee created successfully!');
            $this->resetForm();
            $this->generateStaffId(); // Generate new ID for next employee
            $this->dispatch('close-modal', name: 'add-employee');
            $this->modal('employee-added')->close();
        } catch (\Exception $e) {
            $this->addError('save_error', 'Failed to create employee: ' . $e->getMessage());
        }
    }
    public function resetForm()
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'department_id',
            'position_id',
            'level',
            'step',
            'bank_name',
            'account_number',
            'hire_date',
        ]);
        $this->resetErrorBag();
    }
    protected function generateStaffId()
    {
        $lastEmployee = Employee::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
        $this->staff_id = str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
    public function render()
    {
        return view('livewire.admin.employees.manage-employees');
    }
}
