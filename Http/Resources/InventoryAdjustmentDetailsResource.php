<?php
/** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\InventoryAdjustment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed product
 * @property mixed location
 * @property mixed sections
 * @property mixed user
 */
class InventoryAdjustmentDetailsResource extends JsonResource
{
    /** @var string|InventoryAdjustment */
    public $resource = InventoryAdjustment::class;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'quantity_old' => $this->resource->old_quantity,
            'quantity_actual' => $this->resource->actual_quantity,
            'cost_price_old' => $this->resource->old_cost_price,
            'cost_price_actual' => $this->resource->actual_cost_price,
            'product' => $this->when(
                !is_null($this->product),
                function () {
                    return [
                        'id' => $this->product->id,
                        'name' => $this->product->name,
                        'code' => $this->product->product_code,
                        'cost_price' => $this->product->cost_price,
                    ];
                }
            ),
            'adjustment_type' => $this->resource->adjustment_type,
            'remarks' => $this->resource->remarks,
            'date' => $this->resource->date,
            'location' => $this->when(
                $this->location,
                function () {
                    return [
                        'store' => [
                            'id' => $this->location->id,
                            'name' => $this->location->name,
                        ],
                        'section' => $this->when($this->sections, function () {
                            return [
                                'id' => $this->sections->id,
                                'name' => $this->sections->section_name,
                            ];
                        })
                    ];
                }
            ),
            'user' => $this->when(
                !is_null($this->user),
                function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                    ];
                }
            ),
        ];
    }
}
