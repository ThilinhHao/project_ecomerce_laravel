<?php

namespace App\Repositories\PostComment;

use App\Repositories\Base\BaseRepositoryInterface;

interface PostCommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllComments();
    public function getAllUserComments($userId);
}
