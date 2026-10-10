<?php

namespace App\Console\Commands;

use App\Notifications\ShipmentToArrive;
use App\Notifications\ShipmentToDeparture;
use Illuminate\Console\Command;
use App\Enums\OrderShipmentStatusEnum;
use App\Models\OrderShipment;
use App\Notifications\ActiveDeliveredShipment;
use App\Notifications\ShipmentToNotify;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateDailyNotifications extends Command
{
    protected $signature = 'app:create-daily-notifications';

    protected $description = 'Creates daily notifications of GLS';

    public function handle()
    {
        DB::enableQueryLog();
        $departures = OrderShipment::where('order_shipment_status_id', OrderShipmentStatusEnum::ACTIVE)
            ->where('estimated_time_departure', '<=', Carbon::today())
            ->whereNull('start_date')
            ->get();

        foreach ($departures as $departure) {
            $user = $departure->order->createdBy;
            if (! $user || $user->trashed()) {
                continue;
            }
            $user->notify(new ShipmentToDeparture($departure));
        }

        $arrivals = OrderShipment::where('order_shipment_status_id', OrderShipmentStatusEnum::ACTIVE)
            ->where('estimated_time_arrival', '<=', Carbon::today())
            ->whereNull('end_date')
            ->get();

        foreach ($arrivals as $arrival) {
            $user = $arrival->order->createdBy;
            if (! $user || $user->trashed()) {
                continue;
            }
            $user->notify(new ShipmentToArrive($arrival));
        }

        $delivered = OrderShipment::where('order_shipment_status_id', OrderShipmentStatusEnum::ACTIVE)
            ->whereNotNull('end_date')
            ->get();


        foreach ($delivered as $deliver) {
            $user = $deliver->order->createdBy;
            if (! $user || $user->trashed()) {
                continue;
            }
            $user->notify(new ActiveDeliveredShipment($deliver));
        }

        $toNotify = OrderShipment::where('order_shipment_status_id', OrderShipmentStatusEnum::ACTIVE)
            ->where(function ($query) {
                return $query->whereNull('petition_date')
                    ->orWhere('petition_date', '<', Carbon::today());
            })
            ->get();

        foreach ($toNotify as $notification) {
            $user = $notification->order->createdBy;
            if (! $user || $user->trashed()) {
                continue;
            }
            $user->notify(new ShipmentToNotify($notification));
        }
    }
}
