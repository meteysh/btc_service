<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model implements AccountInterface
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }
}
