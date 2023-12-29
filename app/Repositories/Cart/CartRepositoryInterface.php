<?php

namespace App\Repositories\Cart;

use App\Repositories\Base\BaseRepositoryInterface;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function alreadyCart($userId, $productId);
    public function totalPrice($userId);
    public function getListCart($userId);
    public function updateCartByOrderId($userId, $orderId);
}
