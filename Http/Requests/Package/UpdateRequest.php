<?php

namespace App\Http\Requests\Package;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::PACKAGE_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id_number' => 'sometimes|string', Rule::unique('packages')->ignore($this->id, 'id_number'),
            'name' => 'nullable', 'string',
            'width' => 'nullable','numeric',
            'length' => 'nullable','numeric',
            'height' =>  'nullable','numeric'
        ];
    }
}
