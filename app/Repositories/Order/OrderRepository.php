<?php

namespace App\Repositories\Order;

use App\Http\Constants\OrderConstant;
use App\Http\Constants\PaginateConstant;
use App\Models\Order;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function getOrderBy($userId, $orderNumber)
    {
        return $this->model
                ->where('user_id', $userId)
                ->where('order_number', $orderNumber)
                ->first();
    }

    public function getOrder($id)
    {
        return $this->model->with('cart_info')->find($id);
    }

    public function getOrderYear($year)
    {
        return $this->model
                ->with(['cart_info'])
                ->whereYear('created_at', $year)
                ->where('status', OrderConstant::DEFAULT_DELIVERED)
                ->get()
                ->groupBy(function ($d) {
                    return Carbon::parse($d->created_at)->format('m');
                });
    }

    public function getOrderByUser($userId)
    {
        return $this->model->OrderBy('id', 'DESC')->where('user_id', $userId)->paginate(PaginateConstant::PERPAGE);
    }
}
