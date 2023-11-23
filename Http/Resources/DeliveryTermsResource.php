<?php

namespace App\Http\Resources;

use App\Models\DeliveryTerms;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class DeliveryTermsResource extends JsonResource
{
    /** @var string|DeliveryTerms */
    public $resource = DeliveryTerms::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
        ];
    }
}
