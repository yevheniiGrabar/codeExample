<?php

namespace App\Http\Requests\Role;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::ROLE_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*.module_key' => ['sometimes', 'nullable', 'string'],
            'permissions.*.abilities' => ['sometimes', 'nullable', 'array'],
            'permissions.*.abilities.*.key' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
