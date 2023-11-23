<?php

namespace App\Http\Requests\Company;

use App\Enums\AbilityEnum;
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
        return AbilityResolver::can(AbilityEnum::COMPANY_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [

            //main information
            'company_name' => ['string', 'min:3'],
            'industry' => Rule::exists('industries', 'id'),
            "country" => "integer",
            'street' => 'string',
            'street_2' => 'string',
            'city' => "string",
            'zipcode' => "string",
            "phone_number" => "string",
            "email" => "string",
            'website' => "nullable",
            'currency' => ['nullable',Rule::exists('currencies', 'id')],

            //logo
            'company_logo' => "nullable",

            //billing
            'billing_address' => 'nullable|array',
            'billing_address.*.name' => 'nullable|string',
            'billing_address.*.country' => 'nullable|integer',
            'billing_address.*.street' => 'nullable|string',
            'billing_address.*.street_2' => 'nullable|string',
            'billing_address.*.zipcode' => 'nullable|string',
            'billing_address.*.city' => 'nullable|string',
            'billing_address.*.email' => 'nullable|string',
            'billing_address.*.phone' => 'nullable|string',
            'billing_address.*.contact_name' => 'nullable|string',
            'billing_address.*.is_used_for_delivery' => 'boolean',

            //delivery
            'deliveries' => 'nullable|array',
            'deliveries.*.name' => 'nullable|string',
            'deliveries.*.country' => 'nullable|integer',
            'deliveries.*.street' => 'nullable|string',
            'deliveries.*.street_2' => 'nullable|string',
            'deliveries.*.zipcode' => 'nullable|string',
            'deliveries.*.city' => 'nullable|string',
            'deliveries.*.email' => 'nullable|string',
            'deliveries.*.phone' => 'nullable|string',
            'deliveries.*.contact_person' => 'nullable|string',
        ];
    }

    /**
     * @param Validator|\Illuminate\Contracts\Validation\Validator $validator
     * @return HttpResponseException
     */
    public function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator): HttpResponseException {
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
