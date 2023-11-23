<?php

namespace App\Support\Ability;

use App\Enums\AbilityEnum;
use App\Models\Role;
use App\Models\RoleAbility;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AbilityResolver {
    public static function getSuperAdminAbilityList(): array
    {
        return collect(AbilityEnum::getValues())->map(function ($item) {
            return [
                'ability' => $item,
                'can' => 1
            ];
        })->toArray();
    }

    public static function getUserNoAbilityList(): array
    {
        return collect(AbilityEnum::getValues())->map(function ($item) {
            return [
                'ability' => $item,
                'can' => 0
            ];
        })->toArray();
    }

    public static function can(string $ability): bool
    {
        return RoleAbility::query()
            ->where('role_id', Auth::user()->role_id)
            ->where('ability', $ability)
            ->where('can', 1)
            ->exists();
    }

    public static function abilityGroups() {
        $abilities = AbilityEnum::getValues();

        return self::formatAbilities($abilities);
    }

    public static function groupAbilities(Role $role)
    {
        $abilities = $role->abilities()->pluck('ability')->toArray();

        return self::formatAbilities($abilities);
    }

    /**
     * @param  Collection  $abilities
     * @return mixed
     */
    private static function formatAbilities(array $abilities)
    {
        $groups = [];

        foreach ($abilities as $ability) {
            $groupAndAbility = explode('.', $ability);

            $groups[$groupAndAbility[0]][] = [
                'key' => $groupAndAbility[1],
                'key_name' => ucfirst(str_replace('_', ' ', $groupAndAbility[1])),
            ];
        }

        return collect($groups)->map(function ($item, $key) {
            return [
                'module_key' => $key,
                'abilities' => $item,
                'module_name' => ucfirst(str_replace('_', ' ', $key)),
            ];
        })->values();
    }
}
