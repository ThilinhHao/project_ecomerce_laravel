<?php

namespace App\Http\Controllers;

use App\Http\Constants\OrderConstant;
use App\Http\Constants\PaginateConstant;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Shipping\ShippingRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\SessionTrait;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    use SessionTrait;
    protected OrderRepositoryInterface $orderRepo;
    protected UserRepositoryInterface $userRepo;
    protected ShippingRepositoryInterface $shippingRepo;
    protected CartRepositoryInterface $cartRepo;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        UserRepositoryInterface $userRepo,
        ShippingRepositoryInterface $shippingRepo,
        CartRepositoryInterface $cartRepo
    )
    {
        $this->orderRepo = $orderRepo;
        $this->userRepo = $userRepo;
        $this->shippingRepo = $shippingRepo;
        $this->cartRepo = $cartRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = $this->orderRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'DESC');

        return view('backend.order.index')->with('orders',$orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderStoreRequest $request)
    {
        if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
            $this->error();
            return back();
        }

        $order = new Order();
        $order_data = $request->all();
        $order_data['order_number'] = 'ORD-'.strtoupper(Str::random(10));
        $order_data['user_id'] = $request->user()->id;
        $order_data['shipping_id'] = $request->shipping;
        $shipping = $this->shippingRepo->getNameShipping($order_data['shipping_id']);
        $order_data['sub_total'] = Helper::totalCartPrice();
        $order_data['quantity'] = Helper::cartCount();
        if(session('coupon')){
            $order_data['coupon'] = session('coupon')['value'];
        }

        if($request->shipping){
            if(session('coupon')){
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0] - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0];
            }
        } else {
            if(session('coupon')){
                $order_data['total_amount'] = Helper::totalCartPrice() - session('coupon')['value'];
            }
            else{
                $order_data['total_amount'] = Helper::totalCartPrice();
            }
        }

        $order_data['status'] = OrderConstant::STATUS_NEW;
        if(request('payment_method') == 'paypal'){
            $order_data['payment_method'] = 'paypal';
            $order_data['payment_status'] = 'paid';
        } else{
            $order_data['payment_method'] = 'cod';
            $order_data['payment_status'] = 'Unpaid';
        }
        $order->fill($order_data);
        $order->save();
        if($order)
        $users = $this->userRepo->getAdmin();

        $details=[
            'title'=>'New order created',
            'actionURL'=>route('order.show', $order->id),
            'fas'=>'fa-file-alt'
        ];
        Notification::send($users, new StatusNotification($details));

        if(request('payment_method')=='paypal'){
            return redirect()->route('payment')->with(['id'=>$order->id]);
        }
        else{
            session()->forget('cart');
            session()->forget('coupon');
        }
        $this->cartRepo->updateCartByOrderId(auth()->user()->id, $order->id);

        $this->success();
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = $this->orderRepo->find($id);

        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = $this->orderRepo->find($id);

        return view('backend.order.edit')->with('order',$order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        $order = $this->orderRepo->find($id);

        $data=$request->all();
        if($request->status == OrderConstant::DEFAULT_DELIVERED){
            foreach($order['cart'] as $cart){
                $product = $cart['product'];
                $product['stock'] -= $cart['quantity'];
                $product->save();
            }
        }

        $status = $this->orderRepo->update($id, $data);
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->orderRepo->delete($id);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('order.index');

    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        $order = $this->orderRepo->getOrderBy(auth()->user()->id, $request->order_number);
        if($order){
            if($order->status == OrderConstant::STATUS_NEW){
            $this->success();
            return redirect()->route('home');

            } elseif($order->status == OrderConstant::STATUS_PROCESS){
                $this->success();
                return redirect()->route('home');

            } elseif($order->status == OrderConstant::DEFAULT_DELIVERED){
                $this->success();
                return redirect()->route('home');

            } else{
                $this->error();
                return redirect()->route('home');

            }
        } else{
            $this->error();
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request){
        $order = $this->orderRepo->getOrder($request->id);
        if ($order) {
            $file_name = $order->order_number . '-' . $order->first_name . '.pdf';
            $pdf = PDF::loadView('backend.order.pdf', compact('order'));
            return $pdf->download($file_name);
        }

    }
    // Income chart
    public function incomeChart(){
        $year=\Carbon\Carbon::now()->year;
        $items = $this->orderRepo->getOrderYear($year);

        $result = [];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount = $item->cart_info->sum('amount');
                $m = intval($month);
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }

        $data = [];
        for($i = 1; $i <= 12; $i ++){
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = (!empty($result[$i])) ? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }
}
