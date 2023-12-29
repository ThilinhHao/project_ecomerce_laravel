<?php

namespace App\Repositories\Order;

use App\Repositories\Base\BaseRepositoryInterface;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getOrderBy($userId, $orderNumber);
    public function getOrder($id);
    public function getOrderYear($year);
    public function getOrderByUser($userId);
}
