<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class InventoryStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'location_id' => 'integer|exists:locations,id',
            'product_id' => 'integer|exists:products,id',
            'total_quantity' => 'required|integer',
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
