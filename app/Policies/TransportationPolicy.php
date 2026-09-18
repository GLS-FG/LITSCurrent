<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\OrderShipment;
use App\Models\Transportation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TransportationPolicy
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
    public function view(User $user, Transportation $transportation): bool
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
    public function update(User $user, Transportation $transportation): bool
    {
        $shipment = OrderShipment::findOrFail($transportation->order_shipment_id);
        if(!$shipment->editable){
            return false;
        }
        if(!$transportation->editable){
            return false;
        }
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transportation $transportation): bool
    {
        $shipment = OrderShipment::findOrFail($transportation->order_shipment_id);
        if(!$shipment->editable){
            return false;
        }
        if(!$transportation->editable){
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
    public function restore(User $user, Transportation $transportation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transportation $transportation): bool
    {
        return false;
    }
}
