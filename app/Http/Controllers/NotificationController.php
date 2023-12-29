<?php

namespace App\Http\Controllers;

use App\Traits\SessionTrait;
use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationController extends Controller
{
    use SessionTrait;
    public function index(){
        return view('backend.notification.index');
    }

    public function show(Request $request){
        $notification=Auth()->user()->notifications()->where('id',$request->id)->first();

        if($notification){
            $notification->markAsRead();
            return redirect($notification->data['actionURL']);
        }
    }

    public function delete($id){
        $notification=Notification::find($id);
        if($notification){
            $status=$notification->delete();
            if($status){
                $this->success();
                return back();
            }
            else{
                $this->error();
                return back();
            }
        }
        else{
            $this->error();
            return back();
        }
    }
}
