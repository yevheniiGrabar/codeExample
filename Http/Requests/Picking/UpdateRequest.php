<?php

namespace App\Http\Requests\Picking;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::PICKING_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'lines.*.id' => ['required', 'exists:sale_order_lines,id'],
            'lines.*.locations.*.location_id' => ['required', 'numeric', 'exists:locations,id'],
            'lines.*.locations.*.sub_location_id' => ['sometimes', 'nullable', 'numeric', 'exists:sub_locations,id'],
            'lines.*.locations.*.picked_quantity' => ['required', 'numeric'],

            'lines.*.serial_numbers.*.serial_number' => ['sometimes', 'nullable', 'string'],

            'lines.*.batch_numbers.*.batch_number' => ['sometimes', 'nullable', 'string'],
            'lines.*.batch_numbers.*.expiration_date' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
