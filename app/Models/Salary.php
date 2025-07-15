<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    /** @use HasFactory<\Database\Factories\SalaryFactory> */
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'gross_pay',
        'net_pay',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
