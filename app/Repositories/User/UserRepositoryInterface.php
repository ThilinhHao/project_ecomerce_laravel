<?php

namespace App\Repositories\User;

use App\Repositories\Base\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getDataUser();

    public function UpdatePassword($id, $password);

    public function getAdmin();
}
