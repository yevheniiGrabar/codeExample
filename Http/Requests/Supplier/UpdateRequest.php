<?php

namespace App\Http\Requests\Supplier;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::SUPPLIER_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'code' => 'integer',
            'vat' =>  'string',
            'tax_rate' => Rule::exists('taxes', 'id'),
            'currency' => Rule::exists('currencies', 'id'),
            'payment_terms' => Rule::exists('payment_terms', 'id'),

            'contacts' => 'nullable',
            'contacts.*.name' => 'string',
            'contacts.*.phone' => 'string',
            'contacts.*.email' => 'string',

            'billing' => 'nullable',
            'billing.*.name' => 'nullable',
            'billing.*.street' => 'nullable',
            'billing.*.street_2' => 'nullable',
            'billing.*.zipcode' => 'nullable',
            'billing.*.city' => 'nullable',
            'billing.*.country' => 'nullable',
            'billing.*.phone' => 'nullable',
            'billing.*.email' => 'nullable',
            'billing.*.is_used_for_return' => 'boolean',

            'returns' => 'sometimes|nullable|array',
            'returns.*.id' => ['sometimes', 'nullable', 'exists:supplier_returns,id'],
            'returns.*.is_primary' => 'sometimes|boolean',
            'returns.*.name' => 'sometimes|nullable',
            'returns.*.street' => 'sometimes|nullable',
            'returns.*.zipcode' => 'sometimes|nullable',
            'returns.*.city' => 'sometimes|nullable',
            'returns.*.country' => 'sometimes|nullable',
            'returns.*.contact_person' => 'sometimes|nullable',
            'returns.*.phone' => 'sometimes|nullable',
            'returns.*.email' => 'sometimes|nullable',

            'deleted_returns' => 'array'
        ];
    }
}
