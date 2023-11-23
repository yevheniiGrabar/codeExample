<?php

namespace App\Http\Resources;

use App\Models\LocationProduct;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed $sections
 */
class LocationProductResource extends JsonResource
{
    /** @var LocationProduct */
    public $resource = LocationProduct::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): JsonSerializable|array|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'sections' => $this->whenLoaded('sections', SubLocationResource::collection($this->sections))
        ];
    }
}
