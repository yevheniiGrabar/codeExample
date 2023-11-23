<?php

namespace App\Http\Requests\User;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
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
        return AbilityResolver::can(AbilityEnum::USER_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'last_name' => 'string',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user()->id, 'id')
            ],
            'phone' => 'string',
            'country' => 'exists:countries,id',
            'language' => 'exists:languages,id',
        ];
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
