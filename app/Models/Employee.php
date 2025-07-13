<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'staff_id',
        'department_id',
        'position_id',
        'level',
        'step',
        'bank_id',
        'account_number',
        'hire_date'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
