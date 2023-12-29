<?php

namespace App\Http\Controllers;

use App\Http\Constants\ProductConstant;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use App\Traits\SessionTrait;

class WishlistController extends Controller
{
    use SessionTrait;
    protected WishlistRepositoryInterface $wishlistRepo;
    protected ProductRepositoryInterface $productRepo;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepo,
        ProductRepositoryInterface $productRepo
    )
    {
        $this->wishlistRepo = $wishlistRepo;
        $this->productRepo = $productRepo;
    }

    public function wishlist(Request $request){
        if (empty($request->slug)) {
            $this->error();
            return back();
        }
        $product = $this->productRepo->getFirstProduct($request->slug);

        if (empty($product)) {
            $this->error();
            return back();
        }

        $already_wishlist = $this->wishlistRepo->alreadyWishlist(auth()->user()->id, $product->id);

        if($already_wishlist) {
            $this->error();
            return back();
        } else{
            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product->id;
            $wishlist->price = ($product->price - ($product->price * $product->discount) / ProductConstant::DEFAULT_PERCENT);
            $wishlist->quantity = ProductConstant::DEFAULT_QUANTITY;
            $wishlist->amount = $wishlist->price * $wishlist->quantity;
            if ($wishlist->product->stock < $wishlist->quantity || $wishlist->product->stock <= ProductConstant::PRODUCT_STOCK) return back()->with('error','Stock not sufficient!.');
            $wishlist->save();
        }
        $this->success();
        return back();
    }

    public function wishlistDelete(Request $request){
        $this->wishlistRepo->delete($request->id);
        $this->success();
        return back();

    }
}
