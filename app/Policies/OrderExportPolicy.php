<?php

namespace App\Policies;

use App\Enums\OrderExportStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Models\OrderExport;
use App\Models\User;

class OrderExportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderExport $orderExport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderExport $orderExport): bool
    {
        if($orderExport->order_export_status_id->value === OrderExportStatusEnum::CANCELED->value || $orderExport->order->order_status_id->value === OrderStatusEnum::CLOSED->value){
            return false;
        }
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderExport $orderExport): bool
    {
        if($orderExport->order_export_status_id->value === OrderExportStatusEnum::CANCELED->value || $orderExport->order->order_status_id->value === OrderStatusEnum::CLOSED->value){
            return false;
        }
        if ($user->hasRole(RolesEnum::SUPERADMIN)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderExport $orderExport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderExport $orderExport): bool
    {
        return false;
    }
}
