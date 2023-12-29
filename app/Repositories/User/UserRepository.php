<?php

namespace App\Repositories\User;

use App\Http\Constants\RoleConstant;
use App\Repositories\Base\BaseRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getDataUser()
    {
        return $this->model
            ->select(DB::raw("COUNT(*) as count"), DB::raw("DAYNAME(created_at) as day_name"), DB::raw("DAY(created_at) as day"))
            ->where('created_at', '>', Carbon::today()->subDay(6))
            ->groupBy('day_name', 'day')
            ->orderBy('day')
            ->get();
    }

    public function UpdatePassword($id, $password)
    {
        $record = $this->model->find($id);
        return $record->update(['password'=> Hash::make($password)]);
    }

    public function getAdmin()
    {
        return $this->model->where('role', RoleConstant::ROLE_ADMIN)->get();
    }
}
