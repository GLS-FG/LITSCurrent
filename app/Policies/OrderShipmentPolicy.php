<?php

namespace App\Policies;

use App\Enums\OrderShipmentStatusEnum;
use App\Enums\RolesEnum;
use App\Models\OrderShipment;
use App\Models\User;

class OrderShipmentPolicy
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
    public function view(User $user, OrderShipment $orderShipment): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE, RolesEnum::BILLING])) {
            return true;
        }
        if ($orderShipment->order->contact_id == $user->id) {
            return true;
        }
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
    public function update(User $user, OrderShipment $orderShipment): bool
    {
        if(!$orderShipment->editable || !$orderShipment->order->editable){
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
    public function delete(User $user, OrderShipment $orderShipment): bool
    {
        if(!$orderShipment->editable || !$orderShipment->order->editable){
            return false;
        }
        if ($user->hasRole(RolesEnum::SUPERADMIN)) {
            return true;
        }
        return false;
    }

    public function clone(User $user, OrderShipment $orderShipment): bool
    {
        if(!$orderShipment->clonable || !$orderShipment->order->clonable){
            return false;
        }
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR])) {
            return true;
        }
        return false;
    }

    public function checklist(User $user, OrderShipment $orderShipment): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE, RolesEnum::BILLING])) {
            return true;
        }
        return false;
    }

    public function updateChecklist(User $user, OrderShipment $orderShipment): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE])) {
            return true;
        }
        return false;
    }

    public function attach(User $user, OrderShipment $orderShipment): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderShipment $orderShipment): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN]) && $orderShipment->order_shipment_status_id != OrderShipmentStatusEnum::ACTIVE) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderShipment $orderShipment): bool
    {
        return false;
    }
}
