<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\PostCategory\PostCategoryRequest;
use App\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;
use Illuminate\Support\Str;
class PostCategoryController extends Controller
{

    use SessionTrait, CheckCountSlug;

    protected PostCategoryRepositoryInterface $postCategoryRepo;

    public function __construct(PostCategoryRepositoryInterface $postCategoryRepo)
    {
        $this->postCategoryRepo = $postCategoryRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postCategory = $this->postCategoryRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'DESC');

        return view('backend.postcategory.index')->with('postCategories',$postCategory);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.postcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostCategoryRequest $request)
    {
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->postCategoryRepo->getCountBySlug($slug);

        $this->countSlug($count, $slug);
        $data['slug'] = $slug;

        $status = $this->postCategoryRepo->create($data);
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post-category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $postCategory = $this->postCategoryRepo->findOrFail($id);

        return view('backend.postcategory.edit')->with('postCategory', $postCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostCategoryRequest $request, $id)
    {
        $status=$this->postCategoryRepo->update($id, $request->all());

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post-category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->postCategoryRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post-category.index');
    }
}
