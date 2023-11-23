<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerBillingAddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' =>  ['sometimes', 'string'],
            'street' =>  ['sometimes', 'string'],
            'street_2' => ['sometimes', 'string'],
            'postal' =>  ['sometimes', 'string'],
            'city' =>  ['sometimes', 'string'],
            'country' => 'nullable',Rule::exists('countries','id'),
            'email' =>  'nullable','email',
        ];
    }
}
