<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable=['title','slug','summary','photo','status','is_parent','parent_id','added_by'];

    public function parent_info(){
        return $this->hasOne('App\Models\Category','id','parent_id');
    }

    public function child_cat(){
        return $this->hasMany('App\Models\Category','parent_id','id')->where('status','active');
    }

    public static function getAllParentWithChild(){
        return Category::with('child_cat')->where('is_parent',1)->where('status','active')->orderBy('title','ASC')->get();
    }

    public function products(){
        return $this->hasMany('App\Models\Product','cat_id','id')->where('status','active');
    }

    public function sub_products(){
        return $this->hasMany('App\Models\Product','child_cat_id','id')->where('status','active');
    }

    public static function countActiveCategory(){
        $data=Category::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }
}
