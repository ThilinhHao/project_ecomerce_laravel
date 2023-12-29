<?php

namespace App\Repositories\Product;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllProduct();
    public function getFirstProduct($slug);
    public function getProductBySlug($slug);
    public function getNameProduct($productId);
    public function getProductLimit($limit);
    public function getProductByFeature();
    public function searchProduct($request);
}
