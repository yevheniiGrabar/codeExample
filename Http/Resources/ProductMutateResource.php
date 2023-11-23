<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductMutateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->removeMissingValues(
            [
                'id' => $this->when(!empty($this->resource['id']), $this->resource['id'] ?? null),
                'name' => $this->when(!empty($this->resource['name']), $this->resource['name'] ?? null),
                'code' => $this->when(!empty($this->resource['product_code']), $this->resource['product_code'] ?? null),
            ]
        );
    }
}
