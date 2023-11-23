<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
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
        return $user->hasDirectPermission(Access::VIEW_COMPANIES_LIST);
    }

    /**
     *
     * /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function view(User $user, Company $company): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_COMPANY)
            && $user->companies()->wherePivot('user_id', '=', $user->id);
    }


    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_COMPANY);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function update(User $user, Company $company): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_COMPANY)
            && $user->companies()->wherePivot('company_id', '=', $company->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function delete(User $user, Company $company): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_COMPANY);
    }
}
