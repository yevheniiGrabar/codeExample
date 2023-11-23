<?php

namespace App\Http\Resources;

use App\Models\ShippingAddress;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed company
 */
class ShippingAddressResource extends JsonResource
{
    /**
     * @var string|ShippingAddress
     */
    public $resource = ShippingAddress::class;
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'street' => $this->resource->street,
            'street_2' => $this->resource->street_2,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'city' => $this->resource->city,
            'zipcode' => $this->resource->zipcode,
            'country' => $this->resource->country,
            'contact_person' => $this->resource->contact_person,
            'created_at' => $this->resource->created_at,
            //            'company' => new CompanyResource($this->company),
        ];
    }
}
