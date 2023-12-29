<?php

namespace App\Repositories\Shipping;

use App\Repositories\Base\BaseRepositoryInterface;

interface ShippingRepositoryInterface extends BaseRepositoryInterface
{
    public function getNameShipping($shippingId);
}
