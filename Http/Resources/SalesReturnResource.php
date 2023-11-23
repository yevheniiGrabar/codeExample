<?php

namespace App\Http\Resources;

use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $shipment
 * @property mixed $customer
 */
class SalesReturnResource extends JsonResource
{

    /**
     * @var string|SalesReturn
     */
    public $resource = SalesReturn::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request = null)
    {
        return [
            'id' => $this->resource->id,
            'shipment' => new ShipmentResource($this->shipment),
            'customer' => new CustomerResource($this->customer),
            'return_date' => $this->resource->return_date,
            'status' => $this->resource->status,
        ];
    }
}
