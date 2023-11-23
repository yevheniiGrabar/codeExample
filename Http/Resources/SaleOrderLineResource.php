<?php /** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\Collection;
use App\Models\SaleOrder;
use App\Models\SaleOrderLine;
use App\Models\SubLocation;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed location
 * @property mixed products
 */
class SaleOrderLineResource extends JsonResource
{
    /** @var string|SaleOrderLine */
    public $resource = SaleOrderLine::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'collection' => new CollectionResource($this->collection),
            'collection_name' => $this->resource->collection_name,
            'discount' => $this->resource->discount,
            'unit_price' => $this->resource->unit_price,
            'supplier_registration_no' => $this->resource->supplier_registration_no,
            'quantity' => $this->resource->quantity,
            'picked_quantity' => $this->resource->picked_quantity,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'has_serial_number' => $this->product->has_serial_number,
                    'has_batch_number' => $this->product->has_batch_number,
                    'name' => $this->product->name,
                    'code' => $this->product->product_code,
                    'locations' => $this->product?->locations->map(function ($location) {
                        return [
                            'location_id' => $location->id,
                            'sub_location_id' => $location->pivot->sub_location_id,
                            'location_name' => $location->name,
                            'section_name' => SubLocation::find($location->pivot->sub_location_id)?->section_name,
                            'in_stock' => $location->pivot->in_stock,
                        ];
                    }),
                ];
            }),
            'batch_numbers' => $this->saleOrder->batchNumbers()->where('product_id', $this->product->id)->get(),
            'serial_numbers' => $this->saleOrder->serialNumbers()->where('product_id', $this->product->id)->get(),
            'sale_order' => $this->whenLoaded('saleOrder', function () {
               return [
                   'id' => $this->saleOrder->id,
               ];
            }),
//            'location' => new LocationResource($this->location),
            //'sale_order' => new SaleOrderResource($this->saleOrder),    // Error here
        ];
    }
}
