<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $user
 * @property mixed $location
 * @property mixed $stockCountProduct
 */
class StockCountResource extends JsonResource
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
            'date' => $this->resource->date,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'status' => $this->resource->status,
            'declination_comment' => $this->resource->declination_comment,
            'store' => [
                'id' => $this->location->id,
                'name' => $this->location->name
            ],
            'counts' => $this->whenLoaded(
                'stockCountProduct',
                StockCountProductResource::collection($this->stockCountProduct)
            )
        ];
    }
}
