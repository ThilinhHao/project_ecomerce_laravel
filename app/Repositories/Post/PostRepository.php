<?php

namespace App\Repositories\Post;

use App\Http\Constants\PaginateConstant;
use App\Http\Constants\PostConstant;
use App\Http\Constants\RoleConstant;
use App\Models\Post;
use App\Repositories\Base\BaseRepository;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $post)
    {
        parent::__construct($post);
    }

    public function getAllPost()
    {
        return $this->model->with(['cat_info', 'author_info'])->orderBy('id','DESC')->paginate(PaginateConstant::PERPAGE);
    }

    public function getPostBySlug($slug)
    {
        return $this->model
                ->with(['tag_info','author_info'])
                ->where('slug', $slug)
                ->where('status', RoleConstant::STATUS_ACTIVE)
                ->first();
    }

    public function getPostByStatus()
    {
        return $this->model->where('status','active')->orderBy('id','DESC')->limit(PostConstant::LIMIT_POST)->get();
    }

    public function searchPost($request)
    {
        return $this->model
            ->orwhere('title', 'like', '%'.$request->search.'%')
            ->orwhere('quote', 'like', '%'.$request->search.'%')
            ->orwhere('summary', 'like', '%'.$request->search.'%')
            ->orwhere('description', 'like', '%'.$request->search.'%')
            ->orwhere('slug', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'DESC')
            ->paginate(PostConstant::LIMIT_SEARCH_POST);
    }

    public function getPostByTag($slug)
    {
        return $this->model->where('tags', $slug)->paginate(PostConstant::LIMIT_SEARCH_POST);
    }
}
