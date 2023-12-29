<?php

namespace App\Repositories\Coupon;

use App\Models\Coupon;
use App\Repositories\Base\BaseRepository;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $coupon)
    {
        parent::__construct($coupon);
    }

    public function getCouponByCode($code)
    {
        return $this->model->where('code', $code)->first();
    }
}
