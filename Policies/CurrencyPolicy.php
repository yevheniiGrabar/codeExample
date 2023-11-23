<?php

namespace App\Policies;

use App\Enums\Permissions\Access;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CurrencyPolicy
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
        return $user->hasDirectPermission(Access::VIEW_CURRENCIES_LIST);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Currency $currency
     * @return Response|bool
     */
    public function view(User $user, Currency $currency): Response|bool
    {
        return $user->hasDirectPermission(Access::VIEW_CURRENT_CURRENCY);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->hasDirectPermission(Access::CREATE_CURRENCY);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Currency $currency
     * @return Response|bool
     */
    public function update(User $user, Currency $currency): Response|bool
    {
        return $user->hasDirectPermission(Access::UPDATE_CURRENCY);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Currency $currency
     * @return Response|bool
     */
    public function delete(User $user, Currency $currency): Response|bool
    {
        return $user->hasDirectPermission(Access::DELETE_CURRENCY);
    }
}
