<?php

namespace App\Repositories;


use App\Models\Site;
use App\Repositories\Interfaces\SiteRepositoryInterface;

class SiteRepository implements SiteRepositoryInterface
{
    public function getSiteById(int $id)
    {
        return Site::findOrFail($id);
    }
}
