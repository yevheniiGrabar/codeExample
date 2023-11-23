<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
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
        return AbilityResolver::can(AbilityEnum::PURCHASE_ORDER_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'supplier' => Rule::exists('suppliers', 'id'),
            'purchase_date' => 'date',
            'preferred_delivery_date' => 'date',
            'our_reference' => Rule::exists('users', 'id'),
            'their_reference' => Rule::exists('suppliers', 'id'),
            'payment_term' => Rule::exists('payment_terms', 'id'),
            'delivery_term' => Rule::exists('delivery_terms', 'id'),
            'currency' => Rule::exists('currencies', 'id'),
            'delivery_address' => Rule::exists('delivery_addresses', 'id'),
            'is_billing_for_delivery' => 'boolean',
            'orders' => 'array',
            'orders.*.id' => 'integer',
            'orders.*.product' => Rule::exists('products', 'id'),
            'orders.*.quantity' => 'integer',
            'orders.*.unit_price' => 'numeric',
            'orders.*.discount' => 'numeric',
            'orders.*.tax' => Rule::exists('taxes', 'id'),
            'deleted_orders' => 'array',
        ];
    }
}
