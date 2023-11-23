<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyDeliveryAddressStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function getRules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'postal' => 'nullable|numeric',
            'city' => 'nullable|string',
            'country_id' => ['nullable', Rule::exists('countries','id')],
            'street' => 'nullable|string',
            'street_2' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'is_primary' => 'boolean'
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return self::getRules();
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
