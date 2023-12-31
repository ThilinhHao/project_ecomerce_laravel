<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable=['user_id','product_id','rate','review','status'];

    public function user_info(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function product(){
        return $this->hasOne(Product::class,'id','product_id');
    }

}
