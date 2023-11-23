<?php

namespace App\Http\Resources;

use App\Models\Client;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed shippingAddress
 */
class ClientResource extends JsonResource
{
    /** @var string|Client */
    public $resource;
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'shipping_address' => new ShippingAddressResource($this->shippingAddress)
        ];
    }
}
