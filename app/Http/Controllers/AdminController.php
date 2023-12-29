<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ChangPasswordRequest;
use App\Http\Requests\Admin\SettingsUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\File;
use App\Traits\CallStorageLink;
use App\Traits\SessionTrait;

class AdminController extends Controller
{
    use CallStorageLink, SessionTrait;

    protected UserRepositoryInterface $userRepo;
    protected SettingRepositoryInterface $settingRepo;

    public function __construct(
        UserRepositoryInterface $userRepo,
        SettingRepositoryInterface $settingRepo
        )
    {
        $this->userRepo = $userRepo;
        $this->settingRepo = $settingRepo;
    }

    public function index(){
        $data = $this->userRepo->getDataUser();
        $array[] = ['Name', 'Number'];

        foreach($data as $key => $value) {
            $array[++$key] = [$value->day_name, $value->count];
        }

        return view('backend.index')->with('users', json_encode($array));
    }


    public function profile(){
        $profile = Auth()->user();

        return view('backend.users.profile')->with('profile', $profile);
    }

    public function profileUpdate(Request $request,$id){
        $data = $request->all();
        $status = $this->userRepo->update($id , $data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->back();
    }

    public function settings(){
        $data = $this->settingRepo->getDetail();

        return view('backend.setting')->with('data',$data);
    }

    public function settingsUpdate(SettingsUpdateRequest $request){
        $data = $request->all();

        $settings = $this->settingRepo->getDetail();

        $status = $settings->fill($data)->save();
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('admin');
    }

    public function changePassword(){
        return view('backend.layouts.changePassword');
    }

    public function changPasswordStore(ChangPasswordRequest $request)
    {
        $this->userRepo->UpdatePassword(auth()->user()->id, $request->new_password);

        return redirect()->route('admin')->with('success','Password successfully changed');
    }

    public function storageLink(){
        if(File::exists(public_path('storage'))){
            File::delete(public_path('storage'));

            try{
                $this->artisanCall();
            }
            catch(\Exception $exception){
                $this->exceptionSession();
            }
        }
        else{
            try{
                $this->artisanCall();
            }
            catch(\Exception $exception){
                $this->exceptionSession();
            }
        }
    }
}
