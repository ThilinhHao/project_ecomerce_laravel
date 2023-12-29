<?php

namespace App\Http\Controllers;

use App\Http\Constants\ProductReviewConstant;
use App\Http\Requests\ProductReview\ProductReviewRequest;
use Illuminate\Http\Request;
use App\Notifications\StatusNotification;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\ProductReview\ProductReviewRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\SessionTrait;
use Illuminate\Support\Facades\Notification;

class ProductReviewController extends Controller
{

    use SessionTrait;
    protected ProductReviewRepositoryInterface $productReviewRepo;
    protected ProductRepositoryInterface $productRepo;
    protected UserRepositoryInterface $userRepo;

    public function __construct(
        ProductReviewRepositoryInterface $productReviewRepo,
        ProductRepositoryInterface $productRepo,
        UserRepositoryInterface $userRepo
        )
    {
        $this->productReviewRepo = $productReviewRepo;
        $this->productRepo = $productRepo;
        $this->userRepo = $userRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = $this->productReviewRepo->getAllReview();

        return view('backend.review.index')->with('reviews', $reviews);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductReviewRequest $request)
    {
        $product_info = $this->productRepo->getProductBySlug($request->slug);

        $data = $request->all();
        $data['product_id'] = $product_info->id;
        $data['user_id'] = $request->user()->id;
        $data['status'] = ProductReviewConstant::STATUS_ACTIVE;
        $status = $this->productReviewRepo->create($data);

        $user = $this->userRepo->getAdmin();

        $details=[
            'title'=>'New Product Rating!',
            'actionURL'=>route('product-detail', $product_info->slug),
            'fas'=>'fa-star'
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
        $review = $this->productReviewRepo->find($id);

        return view('backend.review.edit')->with('review', $review);
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
        $data=$request->all();
        $status = $this->productReviewRepo->update($id, $data);
        if($status){
            $this->success();
        } else{
            $this->error();
        }

        return redirect()->route('review.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->productReviewRepo->delete($id);

        if ($status) {
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('review.index');
    }
}
