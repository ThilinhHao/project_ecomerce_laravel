<?php

namespace App\Repositories\PostComment;

use App\Http\Constants\PaginateConstant;
use App\Models\PostComment;
use App\Repositories\Base\BaseRepository;

class PostCommentRepository extends BaseRepository implements PostCommentRepositoryInterface
{
    public function __construct(PostComment $postComment)
    {
        parent::__construct($postComment);
    }

    public function getAllComments()
    {
        return $this->model->with('user_info')->paginate(PaginateConstant::PERPAGE);
    }

    public function getAllUserComments($userId){
        return $this->model->where('user_id', $userId)->with('user_info')->paginate(PaginateConstant::PERPAGE);
    }
}
