<?php

namespace App\Repositories\Wishlist;


use App\Models\Wishlist;
use App\Repositories\Base\BaseRepository;

class WishlistRepository extends BaseRepository implements WishlistRepositoryInterface
{
    public function __construct(Wishlist $wishlist)
    {
        parent::__construct($wishlist);
    }

    public function alreadyWishlist($userId, $productId)
    {
        return $this->model
                ->where('user_id', $userId)
                ->where('cart_id', null)
                ->where('product_id', $productId)
                ->first();
    }

    public function updateWishlist($userId, $cartId)
    {
        return $this->model
                ->where('user_id', $userId)
                ->where('cart_id', null)
                ->update(['cart_id' => $cartId]);
    }
}
