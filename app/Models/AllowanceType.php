<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceType extends Model
{
    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }
}
