<?php

namespace App\Livewire\Payroll;

use App\Models\Salary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaySlipViewer extends Component
{
    public $payslips;
    public function mount()
    {
        $user = Auth::user();


        if ($user->employee) {
            $this->payslips = $user->employee->salaries()
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get();
        } else {
            $this->payslips = collect();
        }
    }
    public function download($id)
    {
        $user = Auth::user();

        $salary = Salary::with('employee') // eager load employee
            ->where('id', $id)
            ->whereHas('employee', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.payslip', [
            'salary' => $salary,
            'employee' => $salary->employee,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Payslip_{$salary->month}_{$salary->year}.pdf");
    }

    public function render()
    {
        return view('livewire.payroll.pay-slip-viewer');
    }
}
