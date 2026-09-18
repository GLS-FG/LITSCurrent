<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\ProjectResource;
use App\Models\Order;

class ApiController extends Controller
{
    public function projects()
    {
        return ProjectResource::collection(Order::where('export_row', true)->whereIn('order_status_id', [OrderStatusEnum::CLOSED])->get());
    }

    public function orders()
    {
        return ProjectResource::collection(Order::where('export_row', true)->whereIn('order_status_id', [OrderStatusEnum::CLOSED])->get());
    }
}
