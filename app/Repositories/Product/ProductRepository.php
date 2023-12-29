<?php

namespace App\Repositories\Product;

use App\Http\Constants\PaginateConstant;
use App\Http\Constants\ProductConstant;
use App\Models\Product;
use App\Repositories\Base\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function getAllProduct()
    {
        return $this->model->with(['cat_info', 'sub_cat_info'])->orderBy('id', 'desc')->paginate(PaginateConstant::PERPAGE);
    }

    public function getFirstProduct($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getProductBySlug($slug){
        return $this->model->with(['cat_info','rel_prods','getReview'])->where('slug',$slug)->first();
    }

    public function getNameProduct($productId)
    {
        return $this->model->where('id', $productId)->pluck('title');
    }

    public function getProductLimit($limit)
    {
        return $this->model->where('status','active')->orderBy('id','DESC')->limit($limit)->get();
    }

    public function getProductByFeature()
    {
        return $this->model
            ->where('status','active')
            ->where('is_featured', ProductConstant::IS_FEATURED)
            ->orderBy('price','DESC')
            ->limit(ProductConstant::LIMIT_PRODUCT)
            ->get();
    }

    public function searchProduct($request)
    {
        return $this->model
            ->orwhere('title', 'like', '%'.$request->search.'%')
            ->orwhere('slug', 'like', '%'.$request->search.'%')
            ->orwhere('description', 'like', '%'.$request->search.'%')
            ->orwhere('summary', 'like', '%'.$request->search.'%')
            ->orwhere('price', 'like','%'.$request->search.'%')
            ->orderBy('id', 'DESC')
            ->paginate(ProductConstant::LIMIT_SEARCH_PRODUCT)
            ->get();

    }
}
