<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use Illuminate\Support\Str;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Repositories\PostTag\PostTagRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;

class PostController extends Controller
{
    use SessionTrait, CheckCountSlug;

    protected PostRepositoryInterface $postRepo;
    protected PostCategoryRepositoryInterface $postCategoryRepo;
    protected PostTagRepositoryInterface $postTagRepo;
    protected UserRepositoryInterface $userRepo;

    public function __construct(
        PostRepositoryInterface $postRepo,
        PostCategoryRepositoryInterface $postCategoryRepo,
        PostTagRepositoryInterface $postTagRepo,
        UserRepositoryInterface $userRepo
    )
    {
        $this->postRepo = $postRepo;
        $this->postCategoryRepo = $postCategoryRepo;
        $this->postTagRepo = $postTagRepo;
        $this->userRepo = $userRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = $this->postRepo->getAllPost();

        return view('backend.post.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->postCategoryRepo->getList();
        $tags = $this->postTagRepo->getList();
        $users = $this->userRepo->getList();

        return view('backend.post.create')->with('users',$users)->with('categories',$categories)->with('tags',$tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostStoreRequest $request)
    {
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->postRepo->getCountBySlug($slug);
        $slug = $this->countSlug($count, $slug);

        $data['slug'] = $slug;
        $tags = $request->input('tags');
        $data['tags'] = $tags ? implode(',', $tags) : '';

        $status = $this->postRepo->create($data);

        if ($status) {
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = $this->postRepo->findOrFail($id);
        $categories = $this->postCategoryRepo->getList();
        $tags = $this->postTagRepo->getList();
        $users = $this->userRepo->getList();
        return view('backend.post.edit')->with('categories',$categories)->with('users',$users)->with('tags',$tags)->with('post',$post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostUpdateRequest $request, $id)
    {
        $data = $request->all();
        $tags = $request->input('tags');
        $data['tags'] = $tags ? implode(',', $tags) : '';

        $status = $this->postRepo->update($id, $data);
        if($status){
            $this->success();
        }
        else{
            $this->error();
        }
        return redirect()->route('post.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->postRepo->delete($id);

        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('post.index');
    }
}
