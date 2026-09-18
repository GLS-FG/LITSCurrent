<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\CustomAgent;
use App\Models\User;

class CustomAgentPolicy
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
    public function view(User $user, CustomAgent $customAgent): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CustomAgent $customAgent): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CustomAgent $customAgent): bool
    {
        if ($user->hasRole(RolesEnum::SUPERADMIN)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CustomAgent $customAgent): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CustomAgent $customAgent): bool
    {
        return false;
    }
}
