<?php

namespace App\Http\Requests\Product;

use App\Enums\AbilityEnum;
use App\Support\Ability\AbilityResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return AbilityResolver::can(AbilityEnum::PRODUCT_INDEX);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'filters' => [
                'nullable',
                'array',
            ],
            'filters.search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'filters.categories' => [
                'nullable',
                'array',
            ],
            'filters.categories.*' => [
                'integer',
                Rule::exists('categories', 'id'),
            ],
            'filters.price_range' => [
                'nullable',
                'array',
                'size:2',
            ],
            'filters.price_range.*' => [
                'numeric',
                'min:0',
            ],
            'filters.quantity_range' => [
                'nullable',
                'array',
                'size:2',
            ],
            'filters.quantity_range.*' => [
                'numeric',
                'min:0',
            ],
            'filters.components' => [
                'nullable',
                'boolean',
            ]
        ];

        $rules['filters'] = 'nullable';

//        if ($this->input('reactive') === '0') {
//            $rules['filters'] = 'required';
//        }

        return $rules;
    }


}
