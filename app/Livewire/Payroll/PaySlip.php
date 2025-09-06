<?php

namespace App\Livewire\Payroll;

use App\Models\Salary;
use Livewire\Component;

class PaySlip extends Component
{
    public $salary;

    public function mount(Salary $salary)
    {
        $this->salary = $salary->load([
            'employee.user',
            'employee.position',
            'employee',
        ]);
    }
    public function render()
    {
        return view('livewire.payroll.pay-slip');
    }
}
