<?php

namespace App\Http\Resources;

use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed customer
 * @property mixed shippingAddress
 */
class ShipmentResource extends JsonResource
{
    /**
     * @var string|Shipment
     */
    public $resource = Shipment::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     * @noinspection PhpUndefinedFieldInspection
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return  [
            'id' => $this->resource->id,
            'sale_order' => new SaleOrderResource($this->saleOrder),
            'customer' => new CustomerResource($this->customer),
            'shipping_address' => new ShippingAddressResource($this->shippingAddress),
            'premium_delivery' => $this->resource->premium_delivery,
            'free_shipping' => $this->resource->free_shipping,
            'item' => $this->resource->item,
            'delivery_date' => $this->resource->delivery_date,
            'is_shipped' => $this->resource->is_shipped,
            'is_paid' => $this->resource->is_paid,
            'is_ready' => $this->resource->is_ready,
            'is_cancelled' => $this->resource->is_cancelled,
        ];
    }
}
