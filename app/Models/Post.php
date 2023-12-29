<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable=['title','tags','summary','slug','description','photo','quote','post_cat_id','post_tag_id','added_by','status'];


    public function cat_info(){
        return $this->hasOne('App\Models\PostCategory','id','post_cat_id');
    }
    public function tag_info(){
        return $this->hasOne('App\Models\PostTag','id','post_tag_id');
    }

    public function author_info(){
        return $this->hasOne('App\User','id','added_by');
    }

    public function comments(){
        return $this->hasMany(PostComment::class)->whereNull('parent_id')->where('status','active')->with('user_info')->orderBy('id','DESC');
    }
    public function allComments(){
        return $this->hasMany(PostComment::class)->where('status','active');
    }

    public static function countActivePost(){
        $data=Post::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }
}
