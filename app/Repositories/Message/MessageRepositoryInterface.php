<?php

namespace App\Repositories\Message;

use App\Repositories\Base\BaseRepositoryInterface;

interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    public function messagePanigate();

    public function getMessageLimit();
}
