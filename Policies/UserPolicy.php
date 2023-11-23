<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
        return $user->hasDirectPermission(Access::VIEW_LIST_USERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function view(User $user, User $model): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_USER);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_USERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function update(User $user, User $model): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function delete(User $user, User $model): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_USERS);
    }
}
