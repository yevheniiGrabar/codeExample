<?php

namespace App\Http\Resources;

use App\Models\SaleOrder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed customer
 * @property mixed saleOrderLines
 */

class ProductSaleOrderResource extends JsonResource
{
    /** @var SaleOrder */
    public $resource = SaleOrder::class;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'customer' => $this->customer->customer_name,
            'quantity' => $this->resource->quantity,
            'shipment_state' => $this->getOrderStatus(),
            'order_date' => $this->resource->order_date
        ];
    }

    /**
     * @return int|null
     */
    public function getOrderStatus(): ?int
    {
        return match (true) {
            $this->resource->is_done => 2,
            $this->resource->is_shipped => 1,
            default => 0,
        };
    }
}
