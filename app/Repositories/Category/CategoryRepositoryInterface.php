<?php

namespace App\Repositories\Category;

use App\Repositories\Base\BaseRepositoryInterface;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllCategory();

    public function orderByTitle();

    public function shiftChild($child_cat_id);

    public function getChildByParentID($id);

    public function getCategoryByStatus();

    public function getProductByCat($slug);

    public function getProductBySubCat($subSlug);
}
