<?php

namespace App\Http\Resources;

use JsonSerializable;
use Illuminate\Http\Request;
use App\Models\ProductPriceHistory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceHistoryResource extends JsonResource
{
    /** @var ProductPriceHistory|string */
    public $resource = ProductPrice::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'old_value' => $this->resource->old_value,
            'new_value' => $this->resource->new_value,
            'date' => $this->resource->created_at
        ];
    }
}
