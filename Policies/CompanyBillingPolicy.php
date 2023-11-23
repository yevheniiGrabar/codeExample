<?php

namespace App\Policies;

use App\Models\CompanyBilling;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CompanyBillingPolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CompanyBilling $companyBilling
     * @return Response|bool
     */
    public function view(User $user, CompanyBilling $companyBilling): Response|bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CompanyBilling $companyBilling
     * @return Response|bool
     */
    public function update(User $user, CompanyBilling $companyBilling): Response|bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CompanyBilling $companyBilling
     * @return Response|bool
     */
    public function delete(User $user, CompanyBilling $companyBilling): Response|bool
    {
        //
    }
}
