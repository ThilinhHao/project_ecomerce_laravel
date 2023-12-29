<?php

namespace App\Traits;

trait SessionTrait
{
    public function success()
    {
        return request()->session()->flash('success','Successfully.');
    }

    public function error()
    {
        return request()->session()->flash('error','Error occurred. Please try again');
    }
}
