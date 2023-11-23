<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\DeliveryAddress;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class DeliveryAddressResource extends JsonResource
{
    /** @var DeliveryAddress|string */
    public $resource = DeliveryAddress::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'street' => $this->resource->street,
            'street_2' => $this->resource->street_2,
            'city' => $this->resource->city,
            'country' => new CountryResource($this->resource->country),
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'contact_person' => $this->resource->contact_person,
            'is_primary' => $this->resource->is_primary
        ];
    }
}
