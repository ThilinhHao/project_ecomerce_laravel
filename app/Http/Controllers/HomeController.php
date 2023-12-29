<?php

namespace App\Http\Controllers;

use App\Http\Constants\OrderConstant;
use App\Http\Requests\Admin\ChangPasswordRequest;
use Illuminate\Http\Request;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\PostComment\PostCommentRepositoryInterface;
use App\Repositories\ProductReview\ProductReviewRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\SessionTrait;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    use SessionTrait;
    protected UserRepositoryInterface $userRepo;
    protected OrderRepositoryInterface $orderRepo;
    protected ProductReviewRepositoryInterface $productReviewRepo;
    protected PostCommentRepositoryInterface $postCommentRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepo,
        OrderRepositoryInterface $orderRepo,
        ProductReviewRepositoryInterface $productReviewRepo,
        PostCommentRepositoryInterface $postCommentRepo
    )
    {
        $this->middleware('auth');
        $this->userRepo = $userRepo;
        $this->orderRepo = $orderRepo;
        $this->productReviewRepo = $productReviewRepo;
        $this->postCommentRepo = $postCommentRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(){
        return view('user.index');
    }

    public function profile(){
        $profile=Auth()->user();
        return view('user.users.profile')->with('profile',$profile);
    }

    public function profileUpdate(Request $request,$id){
        $data = $request->all();
        $status = $this->userRepo->update($id, $data);

        if($status){
            $this->success();
        }
        else{
            $this->error();
        }
        return redirect()->back();
    }

    // Order
    public function orderIndex(){
        $orders = $this->orderRepo->getOrderByUser(auth()->user()->id);

        return view('user.order.index')->with('orders',$orders);
    }

    public function userOrderDelete($id)
    {
        $order = $this->orderRepo->find($id);

        if($order){
           if($order['status'] == OrderConstant::STATUS_PROCESS ||
                $order['status'] == OrderConstant::DEFAULT_DELIVERED ||
                $order['status'] == OrderConstant::STATUS_CANCEL){

                return redirect()->back()->with('error','You can not delete this order now');
           } else{
                $status = $this->orderRepo->delete($id);
                if($status){
                    $this->success();
                } else{
                    $this->error();
                }
                return redirect()->route('user.order.index');
           }
        } else{
            $this->error();
            return redirect()->back();
        }
    }

    public function orderShow($id)
    {
        $order = $this->orderRepo->find($id);

        return view('user.order.show')->with('order',$order);
    }

    // Product Review
    public function productReviewIndex(){
        $reviews = $this->productReviewRepo->getAllUserReview(auth()->user()->id);

        return view('user.review.index')->with('reviews',$reviews);
    }

    public function productReviewEdit($id)
    {
        $review = $this->productReviewRepo->find($id);

        return view('user.review.edit')->with('review',$review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewUpdate(Request $request, $id)
    {
        $data = $request->all();
        $status = $this->productReviewRepo->update($id, $data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }

        return redirect()->route('user.productreview.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewDelete($id)
    {
        $status = $this->productReviewRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('user.productreview.index');
    }

    public function userComment()
    {
        $comments = $this->postCommentRepo->getAllUserComments(auth()->user()->id);

        return view('user.comment.index')->with('comments',$comments);
    }

    public function userCommentDelete($id){
        $status = $this->postCommentRepo->delete($id);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return back();
    }

    public function userCommentEdit($id)
    {
        $comments = $this->postCommentRepo->find($id);

        if($comments){
            return view('user.comment.edit')->with('comment',$comments);
        } else{
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
    public function userCommentUpdate(Request $request, $id)
    {
        $comment = $this->postCommentRepo->find($id);

        if($comment){
            $data = $request->all();

            $status = $this->postCommentRepo->update($id, $data);
            if($status){
                $this->success();
            } else{
                $this->error();
            }
            return redirect()->route('user.post-comment.index');
        } else{
            $this->error();
            return redirect()->back();
        }

    }

    public function changePassword(){
        return view('user.layouts.userPasswordChange');
    }

    public function changPasswordStore(ChangPasswordRequest $request)
    {
        $this->userRepo->update(auth()->user()->id, ['password' => Hash::make($request->new_password)]);

        return redirect()->route('user')->with('success','Password successfully changed');
    }


}
