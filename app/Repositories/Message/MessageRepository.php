<?php

namespace App\Repositories\Message;

use App\Http\Constants\PaginateConstant;
use App\Models\Message;
use App\Repositories\Base\BaseRepository;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    public function __construct(Message $message)
    {
        parent::__construct($message);
    }

    public function messagePanigate()
    {
        return $this->model->panigate(PaginateConstant::MESSAGE_PAGE);
    }

    public function getMessageLimit()
    {
        return $this->model->whereNull('read_at')->limit(PaginateConstant::LIMIT_PERPAGE)->get();
    }

}
