<?php

namespace App\Http\Requests\Package;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::PACKAGE_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_number' => 'sometimes|string|unique:packages,id_number',
            'name' => 'nullable', 'string',
            'width' => 'required','numeric',
            'length' => 'required','numeric',
            'height' =>  'required','numeric'
        ];
    }

    /**
     * @param Validator $validator
     * @return HttpResponseException
     */
    public function failedValidation(Validator $validator): HttpResponseException
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'data' => $validator->errors()
                ]
            )
        );
    }
}
