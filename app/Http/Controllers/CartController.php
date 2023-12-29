<?php

namespace App\Http\Controllers;

use App\Http\Constants\CartConstant;
use App\Http\Constants\ProductConstant;
use App\Http\Requests\Cart\AddToCartRequest;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use App\Traits\SessionTrait;

class CartController extends Controller
{
    use SessionTrait;
    protected CartRepositoryInterface $cartRepo;
    protected ProductRepositoryInterface $productRepo;
    protected WishlistRepositoryInterface $wishlistRepo;

    public function __construct(
        CartRepositoryInterface $cartRepo,
        ProductRepositoryInterface $productRepo,
        WishlistRepositoryInterface $wishlistRepo
    )
    {
        $this->cartRepo = $cartRepo;
        $this->productRepo = $productRepo;
        $this->wishlistRepo = $wishlistRepo;
    }

    public function addToCart(Request $request){
        if (empty($request->slug)) {
            $this->error();
            return back();
        }
        $product = $this->productRepo->getFirstSlug($request->slug);

        if (empty($product)) {
            $this->error();
            return back();
        }

        $already_cart = $this->cartRepo->alreadyCart(auth()->user()->id, $product->id);

        if($already_cart) {
            $already_cart->quantity = $already_cart->quantity + 1;
            $already_cart->amount = $product->price + $already_cart->amount;
            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= ProductConstant::PRODUCT_STOCK) return back()->with('error','Stock not sufficient!.');
            $already_cart->save();

        }else{
            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price - ($product->price * $product->discount) / CartConstant::DEFAULT_PERCENT);
            $cart->quantity = CartConstant::DEFAULT_QUANTITY;
            $cart->amount=$cart->price * $cart->quantity;
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            $cart->save();
            // Wishlist::where('user_id',auth()->user()->id)->where('cart_id', null)->update(['cart_id' => $cart->id]);
            $this->wishlistRepo->updateWishlist(auth()->user()->id, $cart->id);
        }
        $this->success();
        return back();
    }

    public function singleAddToCart(AddToCartRequest $request){
        $product = $this->productRepo->getFirstSlug($request->slug);
        if($product->stock < $request->quant[1]){
            return back()->with('error','Out of stock, You can add other products.');
        }
        if ( ($request->quant[1] < 1) || empty($product) ) {
            $this->error();
            return back();
        }

        $already_cart = $this->cartRepo->alreadyCart(auth()->user()->id, $product->id);

        if($already_cart) {
            $already_cart->quantity = $already_cart->quantity + $request->quant[1];
            $already_cart->amount = ($product->price * $request->quant[1]) + $already_cart->amount;

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= ProductConstant::PRODUCT_STOCK) return back()->with('error','Stock not sufficient!.');

            $already_cart->save();

        }else{

            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price - ($product->price*$product->discount) / ProductConstant::DEFAULT_PERCENT);
            $cart->quantity = $request->quant[1];
            $cart->amount=($product->price * $request->quant[1]);
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= ProductConstant::PRODUCT_STOCK) return back()->with('error','Stock not sufficient!.');
            $cart->save();
        }

        $this->success();
        return back();
    }

    public function cartDelete(Request $request){
        $this->cartRepo->delete($request->id);
        $this->success();
        return back();

    }

    public function cartUpdate(Request $request){
        if($request->quant){
            $error = array();
            $success = '';
            foreach ($request->quant as $k=>$quant) {
                $id = $request->qty_id[$k];
                $cart = Cart::find($id);

                if($quant > 0 && $cart) {
                    if($cart->product->stock < $quant){
                        $this->error();
                        return back();
                    }
                    $cart->quantity = ($cart->product->stock > $quant) ? $quant  : $cart->product->stock;
                    // return $cart;

                    if ($cart->product->stock <= 0) continue;
                    $after_price = ($cart->product->price - ($cart->product->price * $cart->product->discount) / 100);
                    $cart->amount = $after_price * $quant;
                    // return $cart->price;
                    $cart->save();
                    $success = 'Cart successfully updated!';
                } else{
                    $error[] = 'Cart Invalid!';
                }
            }
            return back()->with($error)->with('success', $success);
        }else{
            return back()->with('Cart Invalid!');
        }
    }

    public function checkout(){
        return view('frontend.pages.checkout');
    }
}
