<?php

namespace App\Traits;

trait CheckCountSlug
{
    public function countSlug($count, $slug)
    {
        if($count > 0){
            return $slug.'-'.date('ymdis').'-'.rand(0, 999);
        }
        return $slug;
    }
}
