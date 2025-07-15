<?php

namespace App\Livewire\Payroll;


use App\Models\Employee;
use App\Models\Salary;
use Livewire\Component;
use Illuminate\Support\Carbon;

class GeneratePayslip extends Component
{
    public $month;
    public $year;

    public function mount()
    {
        $this->month = now()->format('F'); // Current month
        $this->year = now()->year;
    }

    public function generate()
    {
        $employees = Employee::with(['allowances', 'deductions'])->get();

        foreach ($employees as $employee) {
            $basic = $employee->basic_salary;
            $totalAllowances = $employee->allowances->sum('amount');
            $totalDeductions = $employee->deductions->sum('amount');
            $gross = $basic + $totalAllowances;
            $net = $gross - $totalDeductions;

            Salary::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $this->month,
                    'year' => $this->year,
                ],
                [
                    'basic_salary' => $basic,
                    'total_allowances' => $totalAllowances,
                    'total_deductions' => $totalDeductions,
                    'gross_pay' => $gross,
                    'net_pay' => $net,
                ]
            );
        }

        session()->flash('success', 'Payslips generated successfully for ' . $this->month . ' ' . $this->year . '.');
    }

    public function render()
    {
        return view('livewire.payroll.generate-payslip');
    }
}
