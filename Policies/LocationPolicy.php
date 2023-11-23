<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LocationPolicy
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
        return $user->hasDirectPermission(Access::VIEW_LIST_LOCATIONS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Location $location
     * @return Response|bool
     */
    public function view(User $user, Location $location): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_LOCATION);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_LOCATION);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Location $location
     * @return Response|bool
     */
    public function update(User $user, Location $location): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_LOCATION);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Location $location
     * @return Response|bool
     */
    public function delete(User $user, Location $location): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_LOCATION);
    }
}
