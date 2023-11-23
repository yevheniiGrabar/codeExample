<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
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
        return $user->hasDirectPermission(Access::VIEW_CATEGORY_LIST);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Category $category
     * @return Response|bool
     */
    public function view(User $user, Category $category): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_CATEGORY_BY_ID);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_CATEGORY);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Category $category
     * @return Response|bool
     */
    public function update(User $user, Category $category): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_CATEGORY);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Category $category
     * @return Response|bool
     */
    public function delete(User $user, Category $category): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_CATEGORY);
    }
}
