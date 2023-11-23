<?php

namespace App\Http\Requests\SaleOrder;

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
        return AbilityResolver::can(AbilityEnum::SALE_ORDER_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer' => Rule::exists('customers', 'id'),
            'order_date' => 'date',
            'preferred_delivery_date' => 'date',
            'our_reference' => Rule::exists('employees', 'id'),
            'their_reference' => Rule::exists('suppliers', 'id'),
            'payment_terms' => Rule::exists('payment_terms', 'id'),
            'delivery_terms' => Rule::exists('delivery_terms', 'id'),
            'currency' => Rule::exists('currencies', 'id'),
            'delivery_address' => 'integer',
            'billing_address' => 'integer',
            'is_billing_for_delivery' => 'boolean',
            'orders' => 'nullable',
            'orders.*.product' => 'integer',
            'orders.*.quantity' => 'integer',
            'orders.*.unit_price' => 'numeric',
            'orders.*.discount' => 'numeric',
            'orders.*.tax' => Rule::exists('taxes', 'id'),
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
