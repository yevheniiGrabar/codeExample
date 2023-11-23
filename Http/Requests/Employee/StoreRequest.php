<?php

namespace App\Http\Requests\Employee;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use App\Traits\CurrentCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::EMPLOYEE_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();

        return [
            'employee_number' => [
                'required',
                'numeric',
                Rule::unique('employees', 'employee_number')
                    ->where('company_id', $defaultCompany->company_id),
            ],
            'name' => 'required|string',
            'job_title' => 'nullable|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'language' => ['nullable', Rule::exists('languages', 'id')]
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
