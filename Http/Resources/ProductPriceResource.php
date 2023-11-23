<?php

namespace App\Http\Resources;

use JsonSerializable;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed currency
 */

class ProductPriceResource extends JsonResource
{
    /** @var ProductPrice|string */
    public $resource = ProductPrice::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'purchase_price' => $this->resource->cost_price,
            'selling_price' => $this->resource->selling_price,
            'currency' => new CurrencyResource($this->currency),
        ];
    }
}
