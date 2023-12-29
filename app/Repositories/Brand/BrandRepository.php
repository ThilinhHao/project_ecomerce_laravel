<?php

namespace App\Repositories\Brand;

use App\Models\Brand;
use App\Repositories\Base\BaseRepository;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
    }

}
