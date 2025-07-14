<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    /** @use HasFactory<\Database\Factories\DeductionFactory> */
    use HasFactory;
    public function type()
    {
        return $this->belongsTo(DeductionType::class, 'deduction_type_id');
    }
    protected $fillable = ['employee_id', 'type', 'amount', 'deduction_type_id', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
