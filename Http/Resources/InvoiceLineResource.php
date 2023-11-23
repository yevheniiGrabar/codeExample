<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceLineResource extends JsonResource
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
//            'purchase_order' => $this->whenLoaded('purchaseOrders', PurchaseOrderResource::collection($this->purchaseOrders)),
            'product' => $this->whenLoaded('products', ProductResource::collection($this->products)),
        ];
    }
}
