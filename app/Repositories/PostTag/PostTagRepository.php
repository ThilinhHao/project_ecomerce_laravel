<?php

namespace App\Repositories\PostTag;

use App\Models\PostTag;
use App\Repositories\Base\BaseRepository;

class PostTagRepository extends BaseRepository implements PostTagRepositoryInterface
{
    public function __construct(PostTag $postTag)
    {
        parent::__construct($postTag);
    }

}
