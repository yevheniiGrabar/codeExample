<?php

namespace App\Http\Requests\Category;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::CATEGORY_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'number' => 'integer'
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

    /**
     * @return bool|int
     */
    public function validateNameAndNumber(): bool|int
    {
        $validator = $this->getValidatorInstance();

        if ($validator->fails()) {
            if ($validator->errors()->has('name')) {
                return 1;
            } elseif ($validator->errors()->has('number')) {
                return 0;
            }
        }

        return true;
    }
}
