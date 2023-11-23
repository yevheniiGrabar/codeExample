<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Enums\Users\RolesEnum;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductPolicy
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
       return $user->hasDirectPermission(Access::VIEW_LIST_PRODUCTS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Product $product
     * @return Response|bool
     */
    public function view(User $user, Product $product): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_PRODUCT);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_PRODUCT);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Product $product
     * @return Response|bool
     */
    public function update(User $user, Product $product): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_PRODUCT);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Product $product
     * @return Response|bool
     */
    public function delete(User $user, Product $product): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_PRODUCT);
    }

    /**
     * @param User $user
     * @param Product $product
     * @return Response|bool
     */
    public function import(User $user, Product $product): Response|bool
    {
        return $user->hasDirectPermission(Access::IMPORT_PRODUCTS);
    }

    /**
     * @param User $user
     * @param Product $product
     * @return Response|bool
     */
    public function export(User $user, Product $product): Response|bool
    {
        return $user->hasDirectPermission(Access::EXPORT_PRODUCTS);
    }
}
