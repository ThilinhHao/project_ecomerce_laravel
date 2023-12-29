<?php

namespace App\Traits;
use Illuminate\Support\Facades\Artisan;

trait CallStorageLink
{
    public function artisanCall()
    {
        Artisan::call('storage:link');
        request()->session()->flash('success', 'Successfully storage linked.');
        return redirect()->back();
    }

    public function exceptionSession()
    {
        request()->session()->flash('success', 'Successfully storage linked.');
        return redirect()->back();
    }
}
