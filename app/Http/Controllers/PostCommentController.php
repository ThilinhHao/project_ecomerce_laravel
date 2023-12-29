<?php

namespace App\Http\Controllers;

use App\Http\Constants\RoleConstant;
use Illuminate\Http\Request;
use App\Notifications\StatusNotification;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\PostComment\PostCommentRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\SessionTrait;
use Illuminate\Support\Facades\Notification;

class PostCommentController extends Controller
{

    use SessionTrait;

    protected PostCommentRepositoryInterface $postCommentRepo;
    protected PostRepositoryInterface $postRepo;
    protected UserRepositoryInterface $userRepo;

    public function __construct(
        PostCommentRepositoryInterface $postCommentRepo,
        PostRepositoryInterface $postRepo,
        UserRepositoryInterface $userRepo
    )
    {
        $this->postCommentRepo = $postCommentRepo;
        $this->postRepo = $postRepo;
        $this->userRepo = $userRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = $this->postCommentRepo->getAllComments();

        return view('backend.comment.index')->with('comments',$comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post_info = $this->postRepo->getPostBySlug($request->slug);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['status'] = RoleConstant::STATUS_ACTIVE;

        $status = $this->postCommentRepo->create($data);

        $user = $this->userRepo->getAdmin();

        $details=[
            'title'=>"New Comment created",
            'actionURL'=>route('blog.detail', $post_info->slug),
            'fas'=>'fas fa-comment'
        ];

        Notification::send($user, new StatusNotification($details));
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comments = $this->postCommentRepo->find($id);
        if ($comments){
            return view('backend.comment.edit')->with('comment',$comments);
        }
        else{
            $this->error();
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $status = $this->postCommentRepo->update($id, $data);
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('comment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->postCommentRepo->delete($id);
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return back();
    }
}
