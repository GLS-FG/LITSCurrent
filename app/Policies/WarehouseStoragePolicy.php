<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Enums\WarehouseStorageStatusEnum;
use App\Models\User;
use App\Models\WarehouseStorage;

class WarehouseStoragePolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, WarehouseStorage $warehouseStorage): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE, RolesEnum::BILLING])) {
            return true;
        }
        if ($warehouseStorage->order->contact_id == $user->id) {
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE])) {
            return true;
        }
        return false;
    }

    public function update(User $user, WarehouseStorage $warehouseStorage): bool
    {
        if(!$warehouseStorage->editable || !$warehouseStorage->order->editable){
            return false;
        }
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR])) {
            return true;
        }
        return false;
    }

    public function delete(User $user, WarehouseStorage $warehouseStorage): bool
    {
        if(!$warehouseStorage->editable || !$warehouseStorage->order->editable){
            return false;
        }
        if ($user->hasRole(RolesEnum::SUPERADMIN)) {
            return true;
        }
        return false;
    }

    public function checklist(User $user, WarehouseStorage $warehouseStorage): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE, RolesEnum::BILLING])) {
            return true;
        }
        return false;
    }

    public function updateChecklist(User $user, WarehouseStorage $warehouseStorage): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WarehouseStorage $warehouseStorage): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN]) && $warehouseStorage->warehouse_storage_status_id != WarehouseStorageStatusEnum::ACTIVE) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WarehouseStorage $warehouseStorage): bool
    {
        return false;
    }
}
