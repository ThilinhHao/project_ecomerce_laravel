<?php

namespace App\Http\Controllers;

use App\Http\Constants\ProductConstant;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Brand;
use App\Repositories\Banner\BannerRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Traits\SessionTrait;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Newsletter\Facades\Newsletter;

class FrontendController extends Controller
{
    use SessionTrait;

    private ProductRepositoryInterface $productRepo;
    private PostRepositoryInterface $postRepo;
    private BannerRepositoryInterface $bannerRepo;
    private CategoryRepositoryInterface $categoryRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        PostRepositoryInterface $postRepo,
        BannerRepositoryInterface $bannerRepo,
        CategoryRepositoryInterface $categoryRepo
    )
    {
        $this->productRepo = $productRepo;
        $this->postRepo = $postRepo;
        $this->bannerRepo = $bannerRepo;
        $this->categoryRepo = $categoryRepo;
    }

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home(){
        $featured = $this->productRepo->getProductByFeature();
        $posts = $this->postRepo->getPostByStatus();
        $banners = $this->bannerRepo->getBannerByStatus();
        $products=$this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODCUCT_CHANGE);
        $category=$this->categoryRepo->getCategoryByStatus();

        return view('frontend.index')
                ->with('featured', $featured)
                ->with('posts', $posts)
                ->with('banners', $banners)
                ->with('product_lists', $products)
                ->with('category_lists', $category);
    }

    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        return view('frontend.pages.contact');
    }

    public function productDetail($slug){
        $product_detail= $this->productRepo->getProductBySlug($slug);

        return view('frontend.pages.product_detail')->with('product_detail',$product_detail);
    }

    public function productGrids(){
        $products=Product::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids);
            // return $products;
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            $products->whereBetween('price',$price);
        }

        $recent_products = $this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODUCT);

        if(!empty($_GET['show'])){
            $products = $products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(9);
        }

        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }

    public function productLists(){
        $products=Product::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            $products->whereIn('cat_id',$cat_ids)->paginate;
        }

        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }

        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);

            $products->whereBetween('price',$price);
        }

        $recent_products = $this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODUCT);

        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(6);
        }

        return view('frontend.pages.product-lists')->with('products',$products)->with('recent_products',$recent_products);
    }

    public function productFilter(Request $request){
            $data = $request->all();
            $showURL = "";
            if(!empty($data['show'])) {
                $showURL .= '&show=' . $data['show'];
            }

            $sortByURL = '';
            if(!empty($data['sortBy'])) {
                $sortByURL .= '&sortBy=' . $data['sortBy'];
            }

            $catURL = "";
            if(!empty($data['category'])) {
                foreach($data['category'] as $category) {
                    if(empty($catURL)) {
                        $catURL .= '&category=' . $category;
                    } else{
                        $catURL .= ',' . $category;
                    }
                }
            }

            $brandURL = "";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .= '&brand=' . $brand;
                    }
                    else{
                        $brandURL .= ',' . $brand;
                    }
                }
            }

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-lists',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }

    public function productSearch(Request $request){
        $recent_products = $this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODUCT);

        $products = $this->productRepo->searchProduct($request);
        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }

    public function productBrand(Request $request){
        $products=Brand::getProductByBrand($request->slug);
        $recent_products = $this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODUCT);
        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
        }

    }
    public function productCat(Request $request){
        $products = $this->categoryRepo->getProductByCat($request->slug);
        $recent_products = $this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODUCT);

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
        }

    }
    public function productSubCat(Request $request){
        $products = $this->categoryRepo->getProductBySubCat($request->sub_slug);

        $recent_products = $this->productRepo->getProductLimit(ProductConstant::LIMIT_PRODUCT);

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->sub_products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->sub_products)->with('recent_products',$recent_products);
        }

    }

    public function blog(){
        $post=Post::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=PostCategory::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            return $cat_ids;
            $post->whereIn('post_cat_id',$cat_ids);
            // return $post;
        }
        if(!empty($_GET['tag'])){
            $slug=explode(',',$_GET['tag']);
            // dd($slug);
            $tag_ids=PostTag::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // return $tag_ids;
            $post->where('post_tag_id',$tag_ids);
            // return $post;
        }

        if(!empty($_GET['show'])){
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate($_GET['show']);
        }
        else{
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate(9);
        }
        $rcnt_post = $this->postRepo->getPostByStatus();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogDetail($slug){
        $post = $this->postRepo->getPostBySlug($slug);
        $rcnt_post = $this->postRepo->getPostByStatus();

        return view('frontend.pages.blog-detail')->with('post',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogSearch(Request $request){
        $rcnt_post = $this->postRepo->getPostByStatus();

        $posts = $this->postRepo->searchPost($request);
        return view('frontend.pages.blog')->with('posts',$posts)->with('recent_posts',$rcnt_post);
    }

    public function blogFilter(Request $request){
        $data=$request->all();
        $catURL="";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .='&category='.$category;
                }
                else{
                    $catURL .=','.$category;
                }
            }
        }

        $tagURL="";
        if(!empty($data['tag'])){
            foreach($data['tag'] as $tag){
                if(empty($tagURL)){
                    $tagURL .='&tag='.$tag;
                }
                else{
                    $tagURL .=','.$tag;
                }
            }
        }
        // return $tagURL;
            // return $catURL;
        return redirect()->route('blog',$catURL.$tagURL);
    }

    public function blogByCategory(Request $request){
        $post=PostCategory::getBlogByCategory($request->slug);
        $rcnt_post = $this->postRepo->getPostByStatus();
        return view('frontend.pages.blog')->with('posts',$post->post)->with('recent_posts',$rcnt_post);
    }

    public function blogByTag(Request $request){
        // dd($request->slug);
        $post = $this->postRepo->getPostByTag($request->slug);

        // return $post;
        $rcnt_post = $this->postRepo->getPostByStatus();
        return view('frontend.pages.blog')->with('posts', $post)->with('recent_posts', $rcnt_post);
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request){
        $data= $request->all();
        if(Auth::attempt(['email' => $data['email'], 'password' => $data['password'],'status'=>'active'])){
            Session::put('user',$data['email']);
            request()->session()->flash('success','Successfully login');
            return redirect()->route('home');
        }
        else{
            request()->session()->flash('error','Invalid email and password pleas try again!');
            return redirect()->back();
        }
    }

    public function logout(){
        Session::forget('user');
        Auth::logout();
        request()->session()->flash('success','Logout successfully');
        return back();
    }

    public function register(){
        return view('frontend.pages.register');
    }

    public function registerSubmit(RegisterRequest $request){
        $data=$request->all();
        $check=$this->create($data);
        Session::put('user',$data['email']);
        if($check){
            request()->session()->flash('success','Successfully registered');
            return redirect()->route('home');
        }
        else{
            request()->session()->flash('error','Please try again!');
            return back();
        }
    }

    public function create(array $data){
        return User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'status'=>'active'
            ]);
    }

    // Reset password
    public function showResetForm(){
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request){
        if(! Newsletter::isSubscribed($request->email)){
                Newsletter::subscribePending($request->email);
                if(Newsletter::lastActionSucceeded()){
                    request()->session()->flash('success','Subscribed! Please check your email');
                    return redirect()->route('home');
                }
                else{
                    Newsletter::getLastError();
                    return back()->with('error','Something went wrong! please try again');
                }
            }
            else{
                request()->session()->flash('error','Already Subscribed');
                return back();
            }
    }

}
