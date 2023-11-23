<?php

namespace App\Http\Resources;

use App\Models\Role;
use App\Support\Ability\AbilityResolver;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed $permissions
 */
class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param null $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => AbilityResolver::groupAbilities(Role::find($this->id)),
        ];

    }
}
