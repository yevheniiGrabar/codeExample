<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UnitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_LIST_UNIT);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Unit $unit
     * @return Response|bool
     */
    public function view(User $user, Unit $unit): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_UNIT);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_UNIT);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Unit $unit
     * @return Response|bool
     */
    public function update(User $user, Unit $unit): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_UNIT);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Unit $unit
     * @return Response|bool
     */
    public function delete(User $user, Unit $unit): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_UNIT);
    }
}
