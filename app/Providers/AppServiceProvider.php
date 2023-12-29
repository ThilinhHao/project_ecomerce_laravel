<?php

namespace App\Providers;

use App\Repositories\Banner\BannerRepository;
use App\Repositories\Banner\BannerRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Base\BaseRepositoryInterface;
use App\Repositories\Brand\BrandRepository;
use App\Repositories\Brand\BrandRepositoryInterface;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Coupon\CouponRepository;
use App\Repositories\Coupon\CouponRepositoryInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Post\PostRepository;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\PostCategory\PostCategoryRepository;
use App\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Repositories\PostComment\PostCommentRepository;
use App\Repositories\PostComment\PostCommentRepositoryInterface;
use App\Repositories\PostTag\PostTagRepository;
use App\Repositories\PostTag\PostTagRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\ProductReview\ProductReviewRepository;
use App\Repositories\ProductReview\ProductReviewRepositoryInterface;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\Shipping\ShippingRepository;
use App\Repositories\Shipping\ShippingRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Wishlist\WishlistRepository;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            BaseRepositoryInterface::class,
            BaseRepository::class
        );

        $this->app->singleton(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->singleton(
            BannerRepositoryInterface::class,
            BannerRepository::class
        );

        $this->app->singleton(
            SettingRepositoryInterface::class,
            SettingRepository::class
        );

        $this->app->singleton(
            BrandRepositoryInterface::class,
            BrandRepository::class
        );

        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        $this->app->singleton(
            CouponRepositoryInterface::class,
            CouponRepository::class
        );

        $this->app->singleton(
            MessageRepositoryInterface::class,
            MessageRepository::class
        );

        $this->app->singleton(
            PostCategoryRepositoryInterface::class,
            PostCategoryRepository::class
        );

        $this->app->singleton(
            PostCommentRepositoryInterface::class,
            PostCommentRepository::class
        );

        $this->app->singleton(
            PostRepositoryInterface::class,
            PostRepository::class
        );

        $this->app->singleton(
            PostTagRepositoryInterface::class,
            PostTagRepository::class
        );

        $this->app->singleton(
            ProductRepositoryInterface::class,
            ProductRepository:: class
        );

        $this->app->singleton(
            ShippingRepositoryInterface::class,
            ShippingRepository::class
        );

        $this->app->singleton(
            WishlistRepositoryInterface::class,
            WishlistRepository::class
        );

        $this->app->singleton(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );

        $this->app->singleton(
            ProductReviewRepositoryInterface::class,
            ProductReviewRepository::class
        );

        $this->app->singleton(
            CartRepositoryInterface::class,
            CartRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
