<?php

namespace App\Http\Resources;

use App\Models\PriceList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed currency
 * @property mixed products
 */
class PriceListResource extends JsonResource
{
    /** @var string| PriceList */
    public $resource = PriceList::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'code' => $this->resource->code,
            'currency' => new CurrencyResource($this->currency),
//            'products' => $this->whenLoaded('products', ProductResource::collection($this->products)),
        ];
    }
}
