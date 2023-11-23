<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $product
 * @property mixed $subLocation
 * @property mixed $locationProduct
 */
class StockCountProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'code' => $this->product->product_code
            ],
            'section' => $this->subLocation ? [
                'id' => $this->subLocation->id,
                'name' => $this->subLocation->section_name,
            ] : null
            ,
            'counted_quantity' => $this->resource->counted_quantity,
            'system_quantity' => $this->getInStock()
        ];
    }
}
