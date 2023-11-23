<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->whenLoaded('customer', CustomerResource::make($this->customer)),
            'preferred_delivery_date' => $this->preferred_delivery_date,
            'picking_status' => (int) $this->picking_status,
            'lines' => $this->whenLoaded('lines', SaleOrderLineResource::collection($this->lines)),
        ];
    }
}
