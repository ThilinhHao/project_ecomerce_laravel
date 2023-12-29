<?php

namespace App\Repositories\Post;

use App\Repositories\Base\BaseRepositoryInterface;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllPost();
    public function getPostBySlug($slug);
    public function getPostByStatus();
    public function searchPost($request);
    public function getPostByTag($slug);
}
