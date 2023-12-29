<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\Base\BaseRepository;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $cart)
    {
        parent::__construct($cart);
    }

    public function alreadyCart($userId, $productId)
    {
        return $this->model
                ->where('user_id', $userId)
                ->where('order_id', null)
                ->where('product_id', $productId)
                ->first();
    }

    public function totalPrice($userId)
    {
        return $this->model->where('user_id', $userId)->where('order_id', null)->sum('price');
    }

    public function getListCart($userId)
    {
        return $this->model->where('user_id', $userId)->where('order_id', null)->get()->toArray();
    }
    public function updateCartByOrderId($userId, $orderId)
    {
        return $this->model->where('user_id', $userId)->where('order_id', null)->update(['order_id' => $orderId]);
    }

}
