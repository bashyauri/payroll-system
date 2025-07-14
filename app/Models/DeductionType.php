<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductionType extends Model
{
    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }
}
