<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    /** @use HasFactory<\Database\Factories\AllowanceFactory> */
    use HasFactory;
    protected $fillable = ['employee_id', 'type', 'amount', 'allowance_type_id', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function type()
    {
        return $this->belongsTo(AllowanceType::class, 'allowance_type_id');
    }
}
