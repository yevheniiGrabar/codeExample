<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyBillingUpdateRequest extends FormRequest
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
            'name' => 'nullable|string',
            'country_id' => 'nullable',Rule::exists('countries','id'),
            'street' => 'nullable|string',
            'street_2' => 'nullable|string',
            'zipcode' => 'nullable|string',
            'city' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'is_used_for_delivery' => 'boolean',
        ];
    }
}
