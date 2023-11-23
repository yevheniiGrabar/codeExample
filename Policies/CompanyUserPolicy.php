<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CompanyUserPolicy
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
        return $user->hasDirectPermission(Access::VIEW_COMPANY_USERS_LIST);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CompanyUser $companyUser
     * @return Response|bool
     */
    public function view(User $user, CompanyUser $companyUser): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_COMPANY_USER);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_COMPANY_USERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CompanyUser $companyUser
     * @return Response|bool
     */
    public function update(User $user, CompanyUser $companyUser): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_COMPANY_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CompanyUser $companyUser
     * @return Response|bool
     */
    public function delete(User $user, CompanyUser $companyUser): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_COMPANY_USERS);
    }
}
