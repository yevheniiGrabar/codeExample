<?php

namespace App\Http\Requests\DeliveryTerms;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::DELIVERY_TERM_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'description' => 'required|string|min:3',
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
