<?php

namespace App\Http\Requests\Adjustment;

use App\Enums\AbilityEnum;
use App\Enums\AdjustmentTypeEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::ADJUSTMENT_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'adjustment_type' => ['required', Rule::in(AdjustmentTypeEnum::getValues())],
            'changed_value' => ['required', 'numeric'],
            'location.store_id' => ['required', 'exists:locations,id'],
            'location.section_id' => ['sometimes', 'nullable', 'exists:sub_locations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
