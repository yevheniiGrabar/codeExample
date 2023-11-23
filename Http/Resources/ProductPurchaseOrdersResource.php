<?php

namespace App\Http\Resources;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed supplier
 * @property mixed receives
 */

class ProductPurchaseOrdersResource extends JsonResource
{
    /** @var PurchaseOrder */
    public $resource = PurchaseOrder::class;

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
            'supplier' => $this->supplier->name,
            'quantity' => $this->resource->quantity,
            'receive_state' => (int) $this->getReceiveStatus(),
            'order_date' => $this->resource->purchase_date
        ];
    }

    /**
     * @return int
     */
    public function getReceiveStatus(): int
    {
        if ($this->resource->is_received) {
            return 2;
        } elseif (is_null($this->resource->is_received) && ($this->resource->is_invoiced || $this->resource->is_booked)) {
            return 1;
        } else {
            return 0;
        }
    }
}
