<?php

namespace App\Repositories\ProductReview;

use App\Http\Constants\PaginateConstant;
use App\Models\ProductReview;
use App\Repositories\Base\BaseRepository;

class ProductReviewRepository extends BaseRepository implements ProductReviewRepositoryInterface
{
    public function __construct(ProductReview $productReview)
    {
        parent::__construct($productReview);
    }

    public function getAllReview()
    {
        return $this->model->with('user_info')->paginate(PaginateConstant::PERPAGE);
    }

    public function getAllUserReview($userId){
        return $this->model->where('user_id', $userId)->with('user_info')->paginate(PaginateConstant::PERPAGE);
    }
}
