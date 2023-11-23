<?php

namespace App\Http\Requests\Supplier;

use App\Enums\AbilityEnum;
use App\Rules\UniqueCodeRule;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::SUPPLIER_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:suppliers,name',
            'code' => ['required', new UniqueCodeRule('suppliers', 'code')],
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
            'billing.*.country' => ['nullable', Rule::exists('countries','id')],
            'billing.*.phone' => 'nullable',
            'billing.*.email' => 'nullable',
            'billing.*.is_used_for_return' => 'boolean',

            'returns' => 'nullable|array',
            'returns.*.is_primary' => 'boolean',
            'returns.*.name' => 'nullable',
            'returns.*.street' => 'nullable',
            'returns.*.zipcode' => 'nullable',
            'returns.*.city' => 'nullable',
            'returns.*.country' => 'nullable',
            'returns.*.contact_person' => 'nullable',
            'returns.*.phone' => 'nullable',
            'returns.*.email' => 'nullable'
        ];
    }
}
