<?php

namespace App\Repositories\Category;

use App\Http\Constants\CategoryConstant;
use App\Models\Category;
use App\Repositories\Base\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function getAllCategory()
    {
        return $this->model->orderBy('id','DESC')->with('parent_info')->paginate(10);
    }

    public function orderByTitle()
    {
        return  $this->model->where('is_parent', CategoryConstant::IS_PARENT)->orderBy('title','ASC')->get();
    }

    public function shiftChild($child_cat_id)
    {
        $getChildId = $this->model->where('parent_id', $child_cat_id)->pluck('id');
        if (count( $getChildId) > 0) {
            return $this->model->whereIn('id', $getChildId)->update(['is_parent'=> CategoryConstant::IS_PARENT]);
        }
    }

    public function getChildByParentID($id)
    {
        return $this->model->where('parent_id',$id)->orderBy('id','ASC')->pluck('title','id');
    }

    public function getCategoryByStatus()
    {
        return $this->model->where('status','active')->where('is_parent', CategoryConstant::IS_PARENT)->orderBy('title','ASC')->get();
    }

    public function getProductByCat($slug)
    {
        return $this->model->with('products')->where('slug', $slug)->first();
    }

    public function getProductBySubCat($subSlug)
    {
        return $this->model->with('sub_products')->where('slug', $subSlug)->first();
    }
}
