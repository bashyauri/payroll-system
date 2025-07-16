<?php

namespace App\Livewire\Payroll;

use Flux\Flux;
use App\Models\Salary;
use Livewire\Component;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use App\Traits\DispatchesActionMessages;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

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
        try {
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

            $this->modal('preview-payslips')->show();

            // 👇 This is what opens the modal
            $this->dispatch('open-modal', ['name' => 'preview-payslips']);
        } catch (ValidationException $e) {
            // Let Livewire handle it as usual
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error in preview()', ['error' => $e]);
            $this->dispatch('preview-payslips-error');

            $this->dispatch('notify', [
                'type' => 'danger',
                'title' => 'Preview Failed',
                'message' => 'An unexpected error occurred while preparing the preview.'
            ]);
        }
    }

    public function dryRun()
    {
        try {
            $this->validate();

            $issues = [];

            // Employees without positions
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

            // Employees with negative pay
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
            $this->modal('dry-run-results')->show();

            if (empty($issues)) {

                $this->dispatch('dry-run-passed', [
                    'type' => 'success',
                    'title' => 'Dry Run Complete',
                    'message' => 'No issues detected in payroll data.'
                ]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error in dryRun()', ['error' => $e]);
            $this->dispatch('dry-run-passed-error');
        }
    }

    public function generate()
    {
        $this->validate();
        $this->showProgress = true;

        try {
            $employees = Employee::with(['position', 'allowances', 'deductions'])->get();
            $this->total = $employees->count();
            $this->processed = 0;

            foreach ($employees as $employee) {
                // Validate base salary
                $base = max(0, $employee->position?->base_salary ?? 0);

                // Ensure allowances and deductions are positive numbers
                $allowances = max(0, $employee->allowances->sum('amount'));
                $deductions = max(0, $employee->deductions->sum('amount'));

                $gross = $base + $allowances;
                $net = max(0, $gross - $deductions); // Prevent negative net pay

                // Additional validation
                if ($base <= 0) {
                    Log::warning("Employee {$employee->id} has invalid base salary: {$base}");
                    continue; // Skip or handle differently
                }

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

            $this->showProgress = false;
            $this->dispatch('payslips-generated');

            session()->flash('success', "Payslips for {$this->month} {$this->year} generated.");
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error generating payslips', ['error' => $e]);
            $this->showProgress = false;
            $this->dispatch('payslips-generated', [
                'type' => 'danger',
                'title' => 'Generation Failed',
                'message' => 'Something went wrong while generating payslips.'
            ]);
        }
    }

    public function closeForm()
    {
        Flux::modals()->close();
    }

    public function render()
    {
        return view('livewire.payroll.generate-payslip', [
            'monthOptions' => collect(range(1, 12))->map(fn($m) => [
                'value' => date('F', mktime(0, 0, 0, $m, 1)),
                'label' => date('F', mktime(0, 0, 0, $m, 1)),
            ]),
            'currentYear' => date('Y'),
            'employeeCount' => Employee::count(),
        ]);
    }
}
