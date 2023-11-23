<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\BillingAddress;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class BillingAddressResource extends JsonResource
{
    /** @var BillingAddress|string */
    public $resource = BillingAddress::class;

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
            'zipcode' => $this->resource->zipcode,
            'city' => $this->resource->city,
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'is_used_for_returns' => $this->resource->is_used_for_returns,
        ];
    }
}
