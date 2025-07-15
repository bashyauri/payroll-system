<?php

namespace App\Livewire\Admin\Employees;

use Flux\Flux;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Deduction;
use Livewire\WithPagination;
use App\Models\DeductionType;
use Illuminate\Validation\ValidationException;

class Deductions extends Component
{
    use WithPagination;
    public $staffIdSearch = '';
    public $selectedEmployee = null;
    public $employees = [];
    public $deduction_type_id;
    public $amount;
    public $note;
    public $deductionTypes = [];
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $editingDeduction = null;
    public ?Deduction $deductionToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->deductionTypes = DeductionType::all();
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
    public function deductions()
    {
        return  Deduction::query()
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
    public function saveDeduction()
    {

        $validated = $this->validate([
            'selectedEmployee.id' => 'required|exists:employees,id',
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $exists = Deduction::where('employee_id', $this->selectedEmployee['id'])
            ->where('deduction_type_id', $validated['deduction_type_id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'deduction_type_id' => 'This deduction has already been added for the employee.',
            ]);
        }



        try {
            $deduction = Deduction::create([
                'employee_id' => $this->selectedEmployee['id'],
                'deduction_type_id' => $validated['deduction_type_id'],
                'amount' => $validated['amount'],
                'note' => $validated['note'],
            ]);

            $this->dispatch('deduction-added');
            $this->dispatch('deduction-added', name: $deduction->employee->user->name);
            $this->resetForm();
            $this->dispatch('close-modal', name: 'add-deduction');
        } catch (\Exception $e) {
            $this->addError('save_error', 'Failed to create deduction: ' . $e->getMessage());
        }
    }
    public function editDeduction(Deduction $deduction): void
    {
        $this->editingDeduction = $deduction;

        $this->selectedEmployee = $deduction->employee; // for display
        $this->deduction_type_id = $deduction->deduction_type_id;
        $this->amount = $deduction->amount;
        $this->note = $deduction->note;

        $this->dispatch('open-modal', name: 'edit-deduction');
    }
    public function updateDeduction()
    {
        $validated = $this->validate([
            'editingDeduction' => 'required',
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            $this->editingDeduction->update([
                'deduction_type_id' => $this->deduction_type_id,
                'amount' => $this->amount,
                'note' => $this->note,
            ]);

            $this->dispatch('deduction-updated', name: $this->editingDeduction->employee->user->name);
            $this->resetForm();
            $this->modal('edit-deduction-' . $this->editingDeduction->id)->close();
        } catch (\Exception $e) {
            $this->addError('update_error', 'Failed to update deduction: ' . $e->getMessage());
        }
    }
    public function confirmDelete(Deduction $deduction): void
    {
        $this->deductionToDelete = $deduction;
        $this->dispatch('open-modal', name: 'confirm-delete-deduction');
    }

    public function deleteDeduction(): void
    {
        try {
            if ($this->deductionToDelete) {
                $employeeName = $this->deductionToDelete->employee->user->name;
                $this->deductionToDelete->delete();

                $this->dispatch('deduction-deleted', name: $employeeName);
                $this->reset('deductionToDelete');
                $this->dispatch('close-modal', name: 'confirm-delete-deduction');

                // Reset page if we deleted the last item on the page
                if ($this->deductions()->count() === 1 && $this->deductions()->currentPage() > 1) {
                    $this->resetPage();
                }
            }
            $this->closeForm();
        } catch (\Exception $e) {
            $this->addError('delete_error', 'Failed to delete deduction: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'staffIdSearch',
            'selectedEmployee',
            'employees',
            'deduction_type_id',
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
        return view('livewire.admin.employees.deductions');
    }
}
