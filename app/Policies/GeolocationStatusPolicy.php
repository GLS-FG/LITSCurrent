<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\GeolocationStatus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GeolocationStatusPolicy
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
    public function view(User $user, GeolocationStatus $geolocationStatus): bool
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
    public function update(User $user, GeolocationStatus $geolocationStatus): bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GeolocationStatus $geolocationStatus): bool
    {
        if ($user->hasRole(RolesEnum::SUPERADMIN)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GeolocationStatus $geolocationStatus): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GeolocationStatus $geolocationStatus): bool
    {
        return false;
    }
}
