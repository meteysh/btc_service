<?php

namespace App\Repositories;


use App\Models\Partner;
use App\Repositories\Interfaces\PartnerRepositoryInterface;

class PartnerRepository implements PartnerRepositoryInterface
{
    public function getPartnerById(int $id)
    {
        return  Partner::findOrFail($id);
    }
}
