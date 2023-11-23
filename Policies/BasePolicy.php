<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use BadMethodCallException;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /** @var array $existsMethods */
    protected array $existingMethods = [];

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (isset($this->existingMethods[$name])) {
            $arguments[] = $this->existingMethods[$name];

            return $this->can(...$arguments);
        }
        throw new BadMethodCallException(sprintf('Method %s not allowed', $name));
    }

    public function __construct()
    {

    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function userAssignedToCompany(User $user): bool
    {
        if (!$company = $this->getCompanyAuthUser()) {
            return false;
        }

        return $this->containsIdCompany($user, $company->id);
    }

    /**
     * @return Company|null
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function getCompanyAuthUser(): ?Company
    {
        return auth()->user()?->companies()->first();
    }

    /**
     * @param User $user
     *
     * @return Collection
     * @noinspection PhpUndefinedFieldInspection
     */
    protected function getCompaniesUser(User $user): Collection
    {
        return $user->companies;
    }

    /**
     * @param User $user
     * @param callable $callback
     *
     * @return bool
     */
    public function permissionAndAssign(User $user, callable $callback): bool
    {
        return $callback() && $this->userAssignedToCompany($user);
    }

    /**
     * @param User $user
     * @param Model $model
     *
     * @return bool
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function containsCompanies(User $user, Model $model): bool
    {
        $companies = $this->getCompaniesUser($user);

        return $companies->contains('id', '=', $model->company_id);
    }

    /**
     * @param User $user
     * @param int $companyId
     *
     * @return bool
     */
    protected function containsIdCompany(User $user, int $companyId): bool
    {
        $companies = $this->getCompaniesUser($user);

        return $companies->contains('id', '=', $companyId);
    }

    /**
     * @param User $user
     * @param string $access
     *
     * @return bool
     */
    protected function can(User $user, string $access): bool
    {
        return $this->permissionAndAssign($user, fn() => $user->hasDirectPermission($access));
    }
}
