<?php

namespace App\Repositories\Shipping;

use App\Models\Shipping;
use App\Repositories\Base\BaseRepository;

class ShippingRepository extends BaseRepository implements ShippingRepositoryInterface
{
    public function __construct(Shipping $shipping)
    {
        parent::__construct($shipping);
    }

    public function getNameShipping($ShippingId)
    {
        return $this->model->where('id', $ShippingId)->pluck('price');
    }
}
