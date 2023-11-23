<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\ProductionOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductionOrderPolicy
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
        return $user->hasDirectPermission(Access::VIEW_PRODUCTION_ORDERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ProductionOrder $productionOrder
     * @return Response|bool
     */
    public function view(User $user, ProductionOrder $productionOrder): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_PRODUCTION_ORDER);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_PRODUCTION_ORDER);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ProductionOrder $productionOrder
     * @return Response|bool
     */
    public function update(User $user, ProductionOrder $productionOrder): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_PRODUCTION_ORDER);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ProductionOrder $productionOrder
     * @return Response|bool
     */
    public function delete(User $user, ProductionOrder $productionOrder): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_PRODUCTION_ORDER);
    }
}
