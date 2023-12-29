<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\PostTag\PostTagStoreRequest;
use App\Http\Requests\PostTag\PostTagUpdateRequest;
use App\Repositories\PostTag\PostTagRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;
use Illuminate\Support\Str;
class PostTagController extends Controller
{

    use SessionTrait, CheckCountSlug;

    protected PostTagRepositoryInterface $postTagRepo;

    public function __construct(PostTagRepositoryInterface $postTagRepo)
    {
        $this->postTagRepo = $postTagRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postTag = $this->postTagRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'DESC');

        return view('backend.posttag.index')->with('postTags',$postTag);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.posttag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostTagStoreRequest $request)
    {

        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->postTagRepo->getCountBySlug($slug);
        $slug = $this->countSlug($count, $slug);
        $data['slug'] = $slug;

        $status = $this->postTagRepo->create($data);
        if ($status) {
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post-tag.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $postTag = $this->postTagRepo->findOrFail($id);

        return view('backend.posttag.edit')->with('postTag', $postTag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostTagUpdateRequest $request, $id)
    {
        $data = $request->all();
        $status = $this->postTagRepo->update($id, $data);
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post-tag.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->postTagRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post-tag.index');
    }
}
