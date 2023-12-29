<?php

namespace App\Repositories\PostCategory;

use App\Models\PostCategory;
use App\Repositories\Base\BaseRepository;

class PostCategoryRepository extends BaseRepository implements PostCategoryRepositoryInterface
{
    public function __construct(PostCategory $postCategory)
    {
        parent::__construct($postCategory);
    }


}
