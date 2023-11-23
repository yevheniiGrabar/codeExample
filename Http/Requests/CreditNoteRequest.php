<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreditNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'customer_id' => Rule::exists('customers', 'id'),
            'date' => 'date',
            'currency_id' => Rule::exists('currencies', 'id'),
            'exchange_rate' => 'decimal',
            'amount' => 'decimal',
            'remaining_credit' => 'decimal'
        ];
    }

    /**
     * @param Validator $validator
     * @return HttpResponseException
     */
    public function failedValidation(Validator $validator
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
