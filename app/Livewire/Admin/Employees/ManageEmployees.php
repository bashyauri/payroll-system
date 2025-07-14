<?php

namespace App\Livewire\Admin\Employees;

use Exception;
use Flux\Flux;
use Carbon\Carbon;
use App\Models\Bank;
use App\Models\User;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageEmployees extends Component
{
    use WithPagination;
    // Employee Fields
    public $staff_id;
    public $user_id;
    public $name;
    public $email;
    public $phone;
    public $department_id;
    public $position_id;
    public $level;
    public $step;
    public $bank_id;
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
    public $banks = [];
    public ?Employee $editingEmployee = null;
    public ?Employee $employeeToDelete = null;

    public function rules()
    {
        $rules = [
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'level' => 'required|string|max:10',
            'step' => 'required|string|max:10',
            'bank_id' => 'required|exists:banks,id',
            'account_number' => 'required|string|max:50|unique:employees,account_number',
            'hire_date' => 'required|date',
        ];

        // Only require user_id and staff_id when creating
        if (!$this->editingEmployee) {
            $rules['user_id'] = 'required|exists:users,id';
        }
        return $rules;
    }
    public function mount()
    {
        $this->departments = Department::all();
        $this->positions = Position::all();
        $this->banks = Bank::all();
        $this->generateStaffId();
    }
    public function sort($field)
    {
        if ($this->sortBy === $field) {
            // Toggle the direction
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new sort field
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
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
        $this->user_id = $user['id']; // Set the user_id when selecting a user
        $this->userSearch = '';
        $this->users = [];
    }

    public function resetUserSelection()
    {
        $this->selectedUser = null;
        $this->user_id = null;
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
                    $q->where('staff_id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%')
                                ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('department', function ($deptQuery) {
                            $deptQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('position', function ($positionQuery) {
                            $positionQuery->where('title', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere('level', 'like', '%' . $this->search . '%')
                        ->orWhere('step', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['user', 'department', 'position'])
            ->when($this->sortBy, function ($query) {
                // Handle different sort fields including relationships
                if ($this->sortBy === 'user.name') {
                    $query->join('users', 'employees.user_id', '=', 'users.id')
                        ->orderBy('users.name', $this->sortDirection);
                } elseif (in_array($this->sortBy, ['department_id', 'position_id'])) {
                    $relation = str_replace('_id', '', $this->sortBy);
                    $query->orderBy(
                        DB::table($relation . 's')
                            ->select('name')
                            ->whereColumn('id', 'employees.' . $this->sortBy),
                        $this->sortDirection
                    );
                } else {
                    $query->orderBy($this->sortBy, $this->sortDirection);
                }
            })
            ->paginate(10);
    }
    public function saveEmployee()
    {
        $validated = $this->validate();
        try {
            $employee =   Employee::create([
                'user_id' => $this->user_id,
                'staff_id' => $validated['staff_id'],
                'department_id' => $validated['department_id'],
                'position_id' => $validated['position_id'],
                'level' => $validated['level'],
                'step' => $validated['step'],
                'bank_id' => $validated['bank_id'],
                'account_number' => $validated['account_number'],
                'hire_date' => $validated['hire_date'] ? Carbon::parse($validated['hire_date']) : null,
            ]);


            $this->dispatch('employee-added', message: 'Employee created successfully!');
            $this->dispatch('employee-updated', name: $employee->user->name);
            $this->resetForm();
            $this->generateStaffId(); // Generate new ID for next employee
            $this->dispatch('close-modal', name: 'add-employee');
            $this->modal('add-employee')->close();
        } catch (Exception $e) {
            $this->addError('save_error', 'Failed to create employee: ' . $e->getMessage());
        }
    }
    public function editEmployee(Employee $employee): void
    {
        $this->editingEmployee = $employee;
        $this->user_id = $employee->user_id; // Add this line
        $this->department_id = $employee?->department_id ?? '';
        $this->position_id = $employee?->position_id ?? '';
        $this->bank_id = $employee?->bank_id ?? '';
        $this->level = $employee?->level ?? 0;
        $this->step = $employee?->step ?? 0;
        $this->account_number = $employee?->account_number ?? '';
        $this->hire_date = $employee->hire_date;


        // Open the modal
        // $this->dispatch('open-modal', name: 'edit-profile-' . $user->id);
    }
    public function updateEmployee()
    {
        $validated = $this->validate();


        try {
            $this->editingEmployee->fill([
                'department_id' => $validated['department_id'],
                'position_id' => $validated['position_id'],
                'level' => $validated['level'],
                'step' => $validated['step'],
                'bank_id' => $validated['bank_id'],
                'account_number' => $validated['account_number'],
            ])->save();

            $this->dispatch('employee-updated', name: $this->editingEmployee->user->name);
            $this->dispatch('close-modal', name: 'edit-employee-' . $this->editingEmployee->id);
            $this->resetForm();
        } catch (Exception $e) {
            Log::error("Employee update failed: " . $e->getMessage());
            $this->addError('update', 'Failed to update employee. Please try again.');
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
            'bank_id',
            'account_number',
            'hire_date',
        ]);
        $this->resetErrorBag();
    }
    public function confirmDelete(Employee $employee): void
    {
        $this->employeeToDelete = $employee;
        $this->modal('edit-employee-' . $employee->id)->close();
    }
    public function deleteEmployee(): void
    {
        try {
            $employeeName = $this->employeeToDelete->user->name;
            $this->employeeToDelete->delete();

            $this->dispatch('employee-deleted', name: $employeeName);
            $this->reset('employeeToDelete');
            $this->dispatch('close-modal', name: 'delete-employee-' . $this->employeeToDelete->id);
        } catch (Exception $e) {
            Log::error("Employee deletion failed: " . $e->getMessage());
            $this->addError('delete', 'Failed to delete user. Please try again.');
        }
    }
    public function closeForm()
    {
        Flux::modals()->close();
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
