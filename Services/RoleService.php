<?php

namespace App\Services;

use App\Traits\CurrentCompany;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class RoleService
{
    public function createRole(array $data): Role
    {
        $role = new Role();

        $role->name = $data['name'];
        $role->company_id = CurrentCompany::getDefaultCompany()->company_id;
        $role->description = $data['description'] ?? null;
        $role->save();

        return $this->permissionsToStore($data, $role);
    }

    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'] ?? $role->name,
            'company_id' => $role->company_id = CurrentCompany::getDefaultCompany()->company_id,
            'description' => $data['description'] ?? $role->description
        ]);

        $role->abilities()->delete();

        return $this->permissionsToStore($data, $role);
    }

    /**
     * @param  array  $data
     * @param  Role  $role
     * @return Role
     */
    private function permissionsToStore(array $data, Role $role): Role
    {
        $permissionsToStore = [];

        if (isset($data['permissions'])) {
            foreach ($data['permissions'] as $module) {
                foreach ($module['abilities'] as $permission) {
                    $permissionsToStore[] = [
                        'ability' => $module['module_key'].'.'.$permission['key'],
                        'can' => 1,
                    ];
                }
            }
        }

        $role->abilities()->createMany($permissionsToStore);

        return $role;
    }
}
