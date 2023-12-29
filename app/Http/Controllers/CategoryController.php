<?php

namespace App\Http\Controllers;

use App\Http\Constants\CategoryConstant;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    use SessionTrait, CheckCountSlug;

    protected CategoryRepositoryInterface $cateRepo;

    public function __construct(CategoryRepositoryInterface $cateRepo)
    {
        $this->cateRepo = $cateRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = $this->cateRepo->getAllCategory();

        return view('backend.category.index')->with('categories', $category);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_cats = $this->cateRepo->orderByTitle();

        return view('backend.category.create')->with('parent_cats', $parent_cats);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->cateRepo->getCountBySlug($slug);
        $slug = $this->countSlug($count, $slug);
        $data['slug'] = $slug;
        $data['is_parent'] = $request->input('is_parent', CategoryConstant::DEFAULT_IS_PARENT);

        $status = $this->cateRepo->create($data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parent_cats = $this->cateRepo->orderByTitle();
        $category =  $this->cateRepo->findOrFail($id);

        return view('backend.category.edit')->with('category',$category)->with('parent_cats',$parent_cats);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, $id)
    {
        $data= $request->all();
        $data['is_parent'] = $request->input('is_parent', CategoryConstant::DEFAULT_IS_PARENT);

        $status = $this->cateRepo->update($id, $data);

        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->cateRepo->delete($id);

        if($status){
            $this->cateRepo->shiftChild($id);
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('category.index');
    }

    public function getChildByParent(Request $request){
        $childCat = $this->cateRepo->getChildByParentID($request->id);

        if(count($childCat) <= 0){
            return response()->json(['status' => false, 'data' => null]);
        } else{
            return response()->json(['status' => true, 'data' => $childCat]);
        }
    }
}
