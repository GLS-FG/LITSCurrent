<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\PrivateDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PrivateDocumentPolicy
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
    public function view(User $user, PrivateDocument $privateDocument): bool
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
    public function update(User $user, PrivateDocument $privateDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PrivateDocument $privateDocument): bool
    {
        if(!$privateDocument->documentable->editable){
            return false;
        }
        if ($user->hasRole([RolesEnum::SUPERADMIN, RolesEnum::OPERATORADMIN])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PrivateDocument $privateDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PrivateDocument $privateDocument): bool
    {
        return false;
    }
}
