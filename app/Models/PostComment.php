<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable=['user_id','post_id','comment','replied_comment','parent_id','status'];

    public function user_info(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function post(){
        return $this->belongsTo(Post::class,'post_id','id');
    }

    public function replies(){
        return $this->hasMany(PostComment::class,'parent_id')->where('status','active');
    }
}
