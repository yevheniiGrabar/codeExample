<?php

namespace App\Http\Resources;

use App\Models\Receive;
use App\Models\ReceiveHistory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed supplier
 * @property mixed purchaseOrders
 */
class ReceiveResource extends JsonResource
{
    /** @var Receive|string */
    public $resource = Receive::class;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchase_order_id,
            'supplier' => $this->when(
                $this->supplier,
                function () {
                    return [
                        'id' => $this->supplier->id,
                        'name' => $this->supplier->name,
                        'code' => $this->supplier->code,
                    ];
                }
            ),
            'date' => $this->receive_date,
            'receives' => $this->receiveHistory->map(
                function ($receiveHistory) {
                    return [
                        'id' => $this->id,
                        'order' => [
                            'id' => $receiveHistory->order_line_id,
                            'product' => [
                                'id' => $receiveHistory->product->id,
                                'name' => $receiveHistory->product->name,
                                'code' => $receiveHistory->product->product_code,
                                'quantity' => $receiveHistory->lines->quantity,
                                'has_serial_number' => $receiveHistory->product->has_serial_number,
                                'has_batch_number' => $receiveHistory->product->has_batch_number
                            ],
                        ],
                        'location' => [
                            'store' => [
                                'id' => $receiveHistory->location->id,
                                'name' => $receiveHistory->location->name,
                            ],
                            'section' => [
                                'id' => $receiveHistory?->subLocation?->id,
                                'name' => $receiveHistory?->subLocation?->section_name,
                            ]
                        ],
                        'received_quantity' => $receiveHistory->received_quantity,
                        'batch_numbers' => $this->batchNumbers()->where('product_id', $receiveHistory->product->id)->get(),
                        'serial_numbers' => $this->serialNumbers()->where('product_id', $receiveHistory->product->id)->get(),
                    ];
                }
            )->toArray(),
        ];
    }
}
