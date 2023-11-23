<?php

namespace App\Http\Requests\Product;

use App\Enums\AbilityEnum;
use App\Rules\UniqueCodeRule;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::PRODUCT_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'code' => ['required', 'string', new UniqueCodeRule('products', 'product_code')],
            'barcode' => 'nullable|string',
            'unit' => Rule::exists('units', 'id'),
            'category' => ['nullable', Rule::exists('categories', 'id')],
            'location' => 'nullable|array',
            'location.store' => Rule::exists('locations', 'id'),
            'location.section' => Rule::exists('sub_locations', 'id'),
            'supplier' => Rule::exists('suppliers', 'id'),
            'tax' => Rule::exists('taxes', 'id'),
            'is_RFID' => 'nullable',
            'is_batch_number' => 'nullable',
            'is_serial_number' => 'nullable',
            'is_components' => 'nullable',
            'inventory' => 'nullable|array',

            'image' => 'sometimes|file|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',

            'prices' => 'array',
            'prices.currency' => Rule::exists('currencies', 'id'),
            'prices.purchase_price' => 'nullable|numeric',
            'prices.selling_price' => 'nullable|numeric',

            'serial_numbers.*.serial_number' => ['sometimes', 'nullable', 'string'],
            'serial_numbers.*.count' => ['sometimes', 'nullable', 'integer', 'min:0'],

            'batch_numbers.*.batch_number' => ['sometimes', 'nullable', 'string'],
            'batch_numbers.*.expiration_date' => ['sometimes', 'nullable', 'date_format:Y-m-d H:i:s'],
            'batch_numbers.*.count' => ['sometimes', 'nullable', 'integer', 'min:0']
        ];
    }


    /**
     * @param Validator|\Illuminate\Contracts\Validation\Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    //                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
