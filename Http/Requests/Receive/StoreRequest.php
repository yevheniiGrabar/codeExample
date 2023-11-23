<?php

namespace App\Http\Requests\Receive;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return AbilityResolver::can(AbilityEnum::RECEIVE_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'purchase_order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'supplier' => ['required', 'integer', 'exists:suppliers,id'],
            'receives' => ['required', 'array'],
            'receives.*.product_order_id' => ['required', 'integer', 'exists:order_lines,id'],
            'receives.*.received_quantity' => ['required', 'integer'],
            'receives.*.location.section' => ['sometimes', 'nullable', 'integer', 'exists:sub_locations,id'],
            'receives.*.location.store' => ['required', 'integer', 'exists:locations,id'],

            'receives.*.serial_numbers' => ['sometimes', 'array'],
            'receives.*.serial_numbers.*.serial_number' => ['sometimes', 'string'],

            'receives.*.batch_numbers' => ['sometimes', 'array'],
            'receives.*.batch_numbers.*.batch_number' => ['sometimes', 'nullable', 'string'],
            'receives.*.batch_numbers.*.expiration_date' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
