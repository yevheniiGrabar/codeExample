<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'id_number' => $this->resource->id_number,
            'name' => $this->resource->name,
            'width' => $this->resource->width,
            'length' => $this->resource->length,
            'height' => $this->resource->height
        ];
    }
}
