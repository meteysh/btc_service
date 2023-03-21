<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Get all of the models that own accounts.
     */
    public function accountable()
    {
        return $this->morphTo();
    }
}

