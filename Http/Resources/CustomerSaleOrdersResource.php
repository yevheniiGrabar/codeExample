<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSaleOrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_sent' => $this->is_sent,
            'is_invoiced' => $this->is_invoiced,
            'is_shipped' => $this->is_shipped,
            'total' => $this->total,
            'order_date' => $this->order_date,
        ];
    }
}
