<?php

namespace App\Repositories\Wishlist;

use App\Repositories\Base\BaseRepositoryInterface;

interface WishlistRepositoryInterface extends BaseRepositoryInterface
{
    public function alreadyWishlist($userId, $productId);
    public function updateWishlist($userId, $cartId);
}
