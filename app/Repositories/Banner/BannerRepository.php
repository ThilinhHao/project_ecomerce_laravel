<?php

namespace App\Repositories\Banner;

use App\Http\Constants\BannerConstant;
use App\Models\Banner;
use App\Repositories\Base\BaseRepository;

class BannerRepository extends BaseRepository implements BannerRepositoryInterface
{
    public function __construct(Banner $banner)
    {
        parent::__construct($banner);
    }

    public function getBannerByStatus()
    {
        return $this->model->where('status','active')->limit(BannerConstant::LIMIT_BANNER)->orderBy('id','DESC')->get();
    }
}
