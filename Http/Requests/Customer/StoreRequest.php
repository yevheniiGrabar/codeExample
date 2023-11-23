<?php

namespace App\Http\Requests\Customer;

use App\Enums\AbilityEnum;
use App\Rules\UniqueCodeRule;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
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
        return AbilityResolver::can(AbilityEnum::CUSTOMER_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "name" => 'string',
            "code" => ['required',new UniqueCodeRule('customers','customer_code')],
            'vat' => [
                'sometimes',
                'nullable',
                Rule::unique('customers', 'vat_number'),
                'regex:/(^ATU\d{8}$)|(^BE0\d{9}$)|(^DE\d{9}$)|(^DK\d{8}$)|(^ES([A-Z0-9]{9})$)|(^FR[A-Z0-9]{2}\d{9}$)|(^GB(\d{9}|\d{12}|(GD|HA)\d{3})$)|(^NL\d{9}B\d{2}$)|(^NO\d{9}$)|(^PL\d{10}$)|(^SE\d{10}01$)|(^UA\d{12}$)|(^LV\d{11}$)|(^LT(\d{9}|\d{12})$)/'
            ],
            'group' => ['integer', Rule::exists('customer_groups', 'id')],
            'tax_rate' => ['integer', Rule::exists('taxes', 'id')],
            'discount' => 'integer',
            'is_person' => 'boolean|nullable',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|string',
            'national_id_number' => 'nullable|string',

            'contacts' => 'array',
            'contacts.*.name' => 'string',
            'contacts.*.phone' => 'string',
            'contacts.*.email' => ['string', Rule::unique('customer_contacts', 'contact_email')],

            'billing' => 'nullable|array',
            'billing.*.name' => 'nullable|string',
            'billing.*.street' => 'nullable|string',
            'billing.*.street_2' => 'nullable|string',
            'billing.*.zipcode' => 'nullable|string',
            'billing.*.city' => 'nullable|string',
            'billing.*.country' => 'nullable|string',
            'billing.*.phone' => 'nullable|string',
            'billing.*.email' => 'nullable|string',
            'billing.*.is_used_for_shipping' => 'boolean',

            'deliveries' => 'nullable|array',
            'deliveries.*.name' => 'nullable|string',
            'deliveries.*.street' => 'nullable|string',
            'deliveries.*.street_2' => 'nullable|string',
            'deliveries.*.zipcode' => 'nullable|string',
            'deliveries.*.city' => 'nullable|string',
            'deliveries.*.country' => 'nullable|string',
            'deliveries.*.contact_person' => 'nullable|string',
            'deliveries.*.phone' => 'nullable|string',
            'deliveries.*.email' => 'nullable|string',
            'deliveries.*.is_primary' => 'boolean',
        ];
    }
}
