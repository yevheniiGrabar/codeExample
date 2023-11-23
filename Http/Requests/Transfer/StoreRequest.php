<?php

namespace App\Http\Requests\Transfer;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::TRANSFER_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product' => ['required', 'exists:products,id'],
            'location_from.store' => ['required', 'exists:locations,id'],
            'location_from.section' => ['sometimes', 'nullable', 'exists:sub_locations,id'],
            'location_to.store' => ['required', 'exists:locations,id'],
            'location_to.section' => ['sometimes', 'nullable', 'exists:sub_locations,id'],
            'quantity' => ['required', 'numeric'],
            'remarks' => ['sometimes', 'nullable'],
        ];
    }
}
