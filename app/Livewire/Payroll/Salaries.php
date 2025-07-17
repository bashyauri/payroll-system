<?php

namespace App\Livewire\Payroll;

use App\Models\Salary;
use Livewire\Component;
use Livewire\WithPagination;

class Salaries extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'month';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showFilters = false;
    public $selectedYear;
    public $selectedMonth;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField',
        'sortDirection',
        'perPage',
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedYear = null;
        $this->selectedMonth = null;
    }

    public function render()
    {
        return view('livewire.payroll.salaries', [
            'salaries' => Salary::query()
                ->with(['employee', 'employee.position'])
                ->when($this->search, function ($query) {
                    $query->whereHas('employee', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->selectedYear, function ($query) {
                    $query->where('year', $this->selectedYear);
                })
                ->when($this->selectedMonth, function ($query) {
                    $query->where('month', $this->selectedMonth);
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'years' => Salary::select('year')->distinct()->orderBy('year', 'desc')->pluck('year'),
            'months' => Salary::select('month')
                ->distinct()
                ->pluck('month')
                ->unique()
                ->sortBy(fn($month) => array_search($month, [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ]))
                ->values(),

        ]);
    }
}