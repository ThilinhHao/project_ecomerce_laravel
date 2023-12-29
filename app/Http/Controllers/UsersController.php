<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\SessionTrait;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    use SessionTrait;
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'ASC');

        return view('backend.users.index')->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        $status = $this->userRepo->create($data);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('users.index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userRepo->findOrFail($id);

        return view('backend.users.edit')->with('user',$user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $data = $request->all();

        $status = $this->userRepo->update($id, $data);
        if ($status) {
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('users.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->userRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('users.index');
    }
}
