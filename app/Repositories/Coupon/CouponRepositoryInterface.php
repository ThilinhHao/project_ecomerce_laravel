<?php

namespace App\Repositories\Coupon;

use App\Repositories\Base\BaseRepositoryInterface;

interface CouponRepositoryInterface extends BaseRepositoryInterface
{
    public function getCouponByCode($code);
}
