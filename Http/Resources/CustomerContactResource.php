<?php

namespace App\Http\Resources;

use App\Models\CustomerContacts;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CustomerContactResource extends JsonResource
{
    /** @var CustomerContacts|string */
    public $resource = CustomerContacts::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'contact_name' => $this->resource->contact_name,
            'contact_phone' => $this->resource->contact_phone,
            'contact_email' => $this->resource->contact_email,
        ];
    }
}
