<?php

namespace App\Http\Controllers;

use App\Http\Constants\ProductConstant;
use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Brand\BrandRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use SessionTrait, CheckCountSlug;

    protected ProductRepositoryInterface $productRepo;
    protected BrandRepositoryInterface $brandRepo;
    protected CategoryRepositoryInterface $categoryRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        BrandRepositoryInterface $brandRepo,
        CategoryRepositoryInterface $categoryRepo
    )
    {
        $this->productRepo = $productRepo;
        $this->brandRepo = $brandRepo;
        $this->categoryRepo = $categoryRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->productRepo->getAllProduct();

        return view('backend.product.index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand = $this->brandRepo->getList();
        $category = $this->categoryRepo->orderByTitle();

        return view('backend.product.create')->with('categories',$category)->with('brands',$brand);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->productRepo->getCountBySlug($slug);
        $slug = $this->countSlug($count, $slug);

        $data['slug'] = $slug;
        $data['is_featured'] = $request->input('is_featured', ProductConstant::DEFAULT_IS_FEATURED);
        $data['size'] = $request->input('size') ? implode(',', $request->input('size')) : '';

        $status = $this->productRepo->create($data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('product.index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = $this->brandRepo->getList();
        $product = $this->productRepo->findOrFail($id);
        $category = $this->categoryRepo->orderByTitle();
        $items = $this->productRepo->findOrFail($id);

        return view('backend.product.edit')->with('product',$product)
                    ->with('brands',$brand)
                    ->with('categories',$category)->with('items',$items);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        $data = $request->all();
        $data['is_featured'] = $request->input('is_featured', ProductConstant::DEFAULT_IS_FEATURED);
        $data['size'] = $request->input('size') ? implode(',', $request->input('size')) : '';

        $status = $this->productRepo->update($id, $data);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->productRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('product.index');
    }
}
