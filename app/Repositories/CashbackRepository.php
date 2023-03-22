<?php

namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\CashbackRepositoryInterface;

class CashbackRepository implements CashbackRepositoryInterface
{
    public function getCashbackById(int $id)
    {
        return  User::findOrFail($id)->cashback;
    }
}
