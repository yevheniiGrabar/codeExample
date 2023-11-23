<?php

namespace App\Http\Requests\Customer;

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
        return AbilityResolver::can(AbilityEnum::CUSTOMER_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
            'code' => 'integer',
            'vat' => ['regex:/(^ATU\d{8}$)|(^BE0\d{9}$)|(^DE\d{9}$)|(^DK\d{8}$)|(^ES([A-Z0-9]{9})$)|(^FR[A-Z0-9]{2}\d{9}$)|(^GB(\d{9}|\d{12}|(GD|HA)\d{3})$)|(^NL\d{9}B\d{2}$)|(^NO\d{9}$)|(^PL\d{10}$)|(^SE\d{10}01$)|(^UA\d{12}$)|(^LV\d{11}$)|(^LT(\d{9}|\d{12})$)/'],
            'tax_rate' => ['integer', Rule::exists('taxes', 'id')],
            'discount' => 'integer',

            'contacts.*.id' => 'integer',
            'contacts.*.name' => 'string',
            'contacts.*.phone' => 'string',
            'contacts.*.email' => ['string'],

            'billing.*.name' => 'string',
            'billing.*.street' => 'string',
            'billing.*.street_2' => 'string',
            'billing.*.zipcode' => 'string',
            'billing.*.city' => 'string',
            'billing.*.country' => 'string',
            'billing.*.phone' => 'string',
            'billing.*.email' => 'string',
            'billing.*.is_used_for_shipping' => 'boolean',

            'deliveries.*.id' => 'integer',
            'deliveries.*.name' => 'string',
            'deliveries.*.street' => 'string',
            'deliveries.*.street_2' => 'string',
            'deliveries.*.zipcode' => 'string',
            'deliveries.*.city' => 'string',
            'deliveries.*.country' => 'string',
            'deliveries.*.contact_person' => 'string',
            'deliveries.*.phone' => 'string',
            'deliveries.*.email' => 'string',
            'deliveries.*.is_primary' => 'boolean',

            'delete_contacts' => 'array',
            'delete_contacts.*' => 'integer',

            'deleted_deliveries' => 'array',
            'deleted_deliveries.*' => 'integer',
        ];
    }

    /**
     * @param Validator|\Illuminate\Contracts\Validation\Validator $validator
     * @return HttpResponseException
     */
    public function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator
    ): HttpResponseException {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    'message' => 'Validation errors',
                    'data' => $validator->errors()
                ]
            )
        );
    }
}
