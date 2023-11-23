<?php

namespace App\Http\Resources;

use App\Models\CustomerContacts;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerContactsResource extends JsonResource
{
    /**
     * @var string|CustomerContacts
     */
    public $resource = CustomerContacts::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request = null): array|\JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->contact_name,
            'phone' => $this->resource->contact_phone,
            'email' => $this->resource->contact_email,
        ];
    }
}
