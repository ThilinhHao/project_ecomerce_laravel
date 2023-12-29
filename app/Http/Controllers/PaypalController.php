<?php

namespace App\Http\Controllers;
use App\Traits\SessionTrait;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Http\Request;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;

class PaypalController extends Controller
{
    use SessionTrait;
    protected CartRepositoryInterface $cartRepo;
    protected ProductRepositoryInterface $productRepo;


    public function __construct(
        CartRepositoryInterface $cartRepo,
        ProductRepositoryInterface $productRepo
        )
    {
        $this->cartRepo = $cartRepo;
        $this->productRepo = $productRepo;
    }
    public function payment()
    {
        $cart = $this->cartRepo->getListCart(auth()->user()->id);

        $data = [];

        $data['items'] = array_map(function ($item) use($cart) {
            $name = $this->productRepo->getNameProduct($item['product_id']);

            return [
                'name' =>$name ,
                'price' => $item['price'],
                'desc'  => 'Thank you for using paypal',
                'qty' => $item['quantity']
            ];
        }, $cart);

        $data['invoice_id'] ='ORD-'.strtoupper(uniqid());
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = route('payment.success');
        $data['cancel_url'] = route('payment.cancel');

        $total = 0;
        foreach($data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $data['total'] = $total;
        if(session('coupon')){
            $data['shipping_discount'] = session('coupon')['value'];
        }
        $this->cartRepo->updateCartByOrderId(auth()->user()->id, session()->get('id'));

        $provider = new ExpressCheckout;

        $response = $provider->setExpressCheckout($data);

        return redirect($response['paypal_link']);
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        dd('Your payment is canceled. You can create cancel page here.');
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request)
    {
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);
        // return $response;

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            request()->session()->flash('success','You successfully pay from Paypal! Thank You');
            session()->forget('cart');
            session()->forget('coupon');
            return redirect()->route('home');
        }

        $this->error();
        return redirect()->back();
    }
}
