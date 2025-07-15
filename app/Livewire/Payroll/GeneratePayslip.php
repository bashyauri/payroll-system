<?php

namespace App\Livewire\Payroll;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Support\Facades\Log;

class GeneratePayslip extends Component
{
    public string $month;
    public int $year;
    public int $processed = 0;
    public int $total = 0;
    public bool $showProgress = false;
    public bool $showPreview = false;
    public bool $showDryRun = false;
    public array $previewData = [];
    public array $dryRunIssues = [];

    protected function rules()
    {
        return [
            'month' => 'required|string',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ];
    }

    public function mount()
    {
        $this->month = now()->format('F');
        $this->year = now()->year;
    }

    public function preview()
    {
        $this->validate();

        $this->previewData = [
            'employee_count' => Employee::count(),
            'month_year' => "{$this->month} {$this->year}",
            'estimated_payroll' => number_format(Employee::with(['position', 'allowances', 'deductions'])
                ->get()
                ->sum(function ($employee) {
                    $base = $employee->position?->base_salary ?? 0;
                    $allowances = $employee->allowances->sum('amount');
                    $deductions = $employee->deductions->sum('amount');
                    return $base + $allowances - $deductions;
                }), 2)
        ];

        $this->showPreview = true;
    }

    public function dryRun()
    {
        $this->validate();

        $issues = [];

        // Check for employees without positions
        $noPosition = Employee::doesntHave('position')->count();
        if ($noPosition > 0) {
            $issues[] = [
                'type' => 'warning',
                'message' => "$noPosition employees without assigned positions",
                'details' => Employee::doesntHave('position')
                    ->get()
                    ->pluck('user.name')
                    ->toArray()
            ];
        }

        // Check for negative net pay
        $negativePayEmployees = Employee::with(['position', 'allowances', 'deductions', 'user'])
            ->get()
            ->filter(function ($employee) {
                $base = $employee->position?->base_salary ?? 0;
                $allowances = $employee->allowances->sum('amount');
                $deductions = $employee->deductions->sum('amount');
                return ($base + $allowances - $deductions) < 0;
            });

        if ($negativePayEmployees->count() > 0) {
            $issues[] = [
                'type' => 'danger',
                'message' => $negativePayEmployees->count() . " employees with negative net pay",
                'details' => $negativePayEmployees->map(function ($employee) {
                    $base = $employee->position?->base_salary ?? 0;
                    $allowances = $employee->allowances->sum('amount');
                    $deductions = $employee->deductions->sum('amount');
                    $net = $base + $allowances - $deductions;
                    return "{$employee->user->name}: " . number_format($net, 2);
                })->toArray()
            ];
        }

        $this->dryRunIssues = $issues;
        $this->showDryRun = true;

        if (empty($issues)) {
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Dry Run Complete',
                'message' => 'No issues detected in payroll data.'
            ]);
        }
    }

    public function generate()
    {
        $this->validate();
        $this->showProgress = true;

        $query = Employee::with(['position', 'allowances', 'deductions']);
        $this->total = $query->count();
        $this->processed = 0;

        // Process in chunks for better performance
        $query->chunk(200, function ($chunk) {
            foreach ($chunk as $employee) {
                $base = $employee->position?->base_salary ?? 0;
                $allowances = $employee->allowances->sum('amount');
                $deductions = $employee->deductions->sum('amount');
                $gross = $base + $allowances;
                $net = $gross - $deductions;

                Salary::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'month' => $this->month,
                        'year' => $this->year,
                    ],
                    [
                        'base_salary' => $base,
                        'total_allowances' => $allowances,
                        'total_deductions' => $deductions,
                        'gross_pay' => $gross,
                        'net_pay' => $net,
                    ]
                );

                $this->processed++;
            }
        });

        $this->showProgress = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Payslips Generated',
            'message' => "Payslips for {$this->month} {$this->year} were successfully generated for {$this->total} employees."
        ]);
    }

    public function render()
    {
        return view('livewire.payroll.generate-payslip', [
            'monthOptions' => [
                ['value' => 'January', 'label' => 'January'],
                ['value' => 'February', 'label' => 'February'],
                ['value' => 'March', 'label' => 'March'],
                ['value' => 'April', 'label' => 'April'],
                ['value' => 'May', 'label' => 'May'],
                ['value' => 'June', 'label' => 'June'],
                ['value' => 'July', 'label' => 'July'],
                ['value' => 'August', 'label' => 'August'],
                ['value' => 'September', 'label' => 'September'],
                ['value' => 'October', 'label' => 'October'],
                ['value' => 'November', 'label' => 'November'],
                ['value' => 'December', 'label' => 'December'],
            ],
            'currentYear' => date('Y'),

            'employeeCount' => Employee::count(),
        ]);
    }
}
