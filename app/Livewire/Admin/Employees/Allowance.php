<?php

namespace App\Livewire\Admin\Employees;

use Flux\Flux;
use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;
use App\Models\AllowanceType;
use App\Models\Allowance as ModelsAllowance;
use Illuminate\Validation\ValidationException;

class Allowance extends Component
{
    use WithPagination;
    public $staffIdSearch = '';
    public $selectedEmployee = null;
    public $employees = [];
    public $allowance_type_id;
    public $amount;
    public $note;
    public $allowanceTypes = [];
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $editingAllowance = null;
    public ?ModelsAllowance $allowanceToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->allowanceTypes = AllowanceType::all();
    }
    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }
    #[\Livewire\Attributes\Computed]
    public function allowances()
    {
        return  ModelsAllowance::query()
            ->with(['employee.user', 'type'])
            ->when($this->search, function ($query) {
                $query->whereHas('employee.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('employee', function ($q) {
                        $q->where('staff_id', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('type', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('amount', 'like', '%' . $this->search . '%')
                    ->orWhere('note', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
    public function updatedStaffIdSearch()
    {
        if (strlen($this->staffIdSearch) > 2) {
            $this->employees = Employee::query()
                ->where('staff_id', 'like', '%' . $this->staffIdSearch . '%')
                ->orWhereHas('user', function ($query) {
                    $query->where('name', 'like', '%' . $this->staffIdSearch . '%');
                })
                ->with('user')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->employees = [];
        }
    }
    public function selectEmployee($employee)
    {
        $this->selectedEmployee = $employee;
        $this->staffIdSearch = '';
        $this->employees = [];
    }
    public function resetEmployeeSelection()
    {
        $this->selectedEmployee = null;
        $this->staffIdSearch = '';
        $this->employees = [];
    }
    public function saveAllowance()
    {

        $validated = $this->validate([
            'selectedEmployee.id' => 'required|exists:employees,id',
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $exists = ModelsAllowance::where('employee_id', $this->selectedEmployee['id'])
            ->where('allowance_type_id', $validated['allowance_type_id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'allowance_type_id' => 'This allowance has already been added for the employee.',
            ]);
        }



        try {
            $allowance = ModelsAllowance::create([
                'employee_id' => $this->selectedEmployee['id'],
                'allowance_type_id' => $validated['allowance_type_id'],
                'amount' => $validated['amount'],
                'note' => $validated['note'],
            ]);

            $this->dispatch('allowance-added');
            $this->dispatch('allowance-added', name: $allowance->employee->user->name);
            $this->resetForm();
            $this->dispatch('close-modal', name: 'add-allowance');
        } catch (\Exception $e) {
            $this->addError('save_error', 'Failed to create allowance: ' . $e->getMessage());
        }
    }
    public function editAllowance(ModelsAllowance $allowance): void
    {
        $this->editingAllowance = $allowance;

        $this->selectedEmployee = $allowance->employee; // for display
        $this->allowance_type_id = $allowance->allowance_type_id;
        $this->amount = $allowance->amount;
        $this->note = $allowance->note;

        $this->dispatch('open-modal', name: 'edit-allowance');
    }
    public function updateAllowance()
    {
        $validated = $this->validate([
            'editingAllowance' => 'required',
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            $this->editingAllowance->update([
                'allowance_type_id' => $this->allowance_type_id,
                'amount' => $this->amount,
                'note' => $this->note,
            ]);

            $this->dispatch('allowance-updated', name: $this->editingAllowance->employee->user->name);
            $this->resetForm();
            $this->modal('edit-allowance-' . $this->editingAllowance->id)->close();
        } catch (\Exception $e) {
            $this->addError('update_error', 'Failed to update allowance: ' . $e->getMessage());
        }
    }
    public function confirmDelete(ModelsAllowance $allowance): void
    {
        $this->allowanceToDelete = $allowance;
        $this->dispatch('open-modal', name: 'confirm-delete-allowance');
    }

    public function deleteAllowance(): void
    {
        try {
            if ($this->allowanceToDelete) {
                $employeeName = $this->allowanceToDelete->employee->user->name;
                $this->allowanceToDelete->delete();

                $this->dispatch('allowance-deleted', name: $employeeName);
                $this->reset('allowanceToDelete');
                $this->dispatch('close-modal', name: 'confirm-delete-allowance');

                // Reset page if we deleted the last item on the page
                if ($this->allowances()->count() === 1 && $this->allowances()->currentPage() > 1) {
                    $this->resetPage();
                }
            }
            $this->closeForm();
        } catch (\Exception $e) {
            $this->addError('delete_error', 'Failed to delete allowance: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'staffIdSearch',
            'selectedEmployee',
            'employees',
            'allowance_type_id',
            'amount',
            'note',
        ]);
        $this->resetErrorBag();
    }
    public function closeForm()
    {
        Flux::modals()->close();
    }

    public function render()
    {
        return view('livewire.admin.employees.allowance');
    }
}
