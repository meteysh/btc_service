<?php

namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getUserById(int $id)
    {
        return  User::findOrFail($id);
    }
}
