<?php


namespace App\Classes\Permissions;

use App\Enums\Permissions\Access;
use App\Enums\Users\RolesEnum;
use ReflectionException;

class Accessor
{

    /**
     * @return array
     * @throws ReflectionException
     */
    public function getSuperAdminAccess(): array
    {
        return Access::getAllValues();
    }
}
