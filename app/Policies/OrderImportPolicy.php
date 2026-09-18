<?php

namespace App\Policies;

use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Models\OrderImport;
use App\Models\User;

class OrderImportPolicy
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
    public function view(User $user, OrderImport $orderImport): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE, RolesEnum::BILLING])) {
            return true;
        }
        if ($orderImport->order->contact_id == $user->id) {
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
    public function update(User $user, OrderImport $orderImport): bool
    {
        if($orderImport->order_import_status_id->value === OrderImportStatusEnum::CANCELED->value || $orderImport->order->order_status_id->value === OrderStatusEnum::CLOSED->value){
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
    public function delete(User $user, OrderImport $orderImport): bool
    {
        if($orderImport->order_import_status_id->value === OrderImportStatusEnum::CANCELED->value || $orderImport->order->order_status_id->value === OrderStatusEnum::CLOSED->value){
            return false;
        }
        if ($user->hasRole(RolesEnum::SUPERADMIN)) {
            return true;
        }
        return false;
    }

    public function checklist(User $user, OrderImport $orderImport): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE, RolesEnum::BILLING])) {
            return true;
        }
        return false;
    }

    public function updateChecklist(User $user, OrderImport $orderImport): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN, RolesEnum::OPERATOR, RolesEnum::WAREHOUSE])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderImport $orderImport): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN]) && $orderImport->order_import_status_id != OrderImportStatusEnum::ACTIVE) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderImport $orderImport): bool
    {
        return false;
    }
}
