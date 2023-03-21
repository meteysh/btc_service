<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
