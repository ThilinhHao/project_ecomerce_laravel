<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\Coupon\CouponStoreRequest;
use App\Http\Requests\Coupon\CouponUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Coupon\CouponRepositoryInterface;
use App\Traits\SessionTrait;

class CouponController extends Controller
{

    use SessionTrait;

    protected CouponRepositoryInterface $couponRepo;
    protected CartRepositoryInterface $cartRepo;

    public function __construct(
        CouponRepositoryInterface $couponRepo,
        CartRepositoryInterface $cartRepo
    )
    {
        $this->couponRepo = $couponRepo;
        $this->cartRepo = $cartRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupon = $this->couponRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'DESC');

        return view('backend.coupon.index')->with('coupons',$coupon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CouponStoreRequest $request)
    {
        $status = $this->couponRepo->create($request->all());

        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('coupon.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon = $this->couponRepo->findOrFail($id);

        return view('backend.coupon.edit')->with('coupon', $coupon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CouponUpdateRequest $request, $id)
    {
        $status = $this->couponRepo->update($id, $request->all());

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('coupon.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status= $this->couponRepo->delete($id);

        if ($status) {
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('coupon.index');

    }

    public function couponStore(Request $request){

        $coupon = $this->couponRepo->getCouponByCode($request->code);

        if(!$coupon){
            $this->error();
            return back();
        }

        if($coupon){
            $total_price = $this->cartRepo->totalPrice(auth()->user()->id);

            session()->put('coupon',[
                'id' => $coupon->id,
                'code' => $coupon->code,
                'value' => $coupon->discount($total_price)
            ]);

            $this->success();
            return redirect()->back();
        }
    }
}
