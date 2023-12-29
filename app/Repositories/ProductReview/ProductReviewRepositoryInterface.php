<?php

namespace App\Repositories\ProductReview;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProductReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllReview();
    public function getAllUserReview($userId);
}
