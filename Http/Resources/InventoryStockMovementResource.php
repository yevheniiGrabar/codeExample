<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\InventoryStockMovement;
use App\Traits\CurrentCompany;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed inventory
 * @property mixed user
 * @property mixed inventoryOld
 * @property mixed inventoryNew
 * @property mixed locationFrom
 * @property mixed locationTo
 * @property mixed sectionFrom
 * @property mixed sectionTo
 * @property mixed product
 */
class InventoryStockMovementResource extends JsonResource
{

    /** @var string|InventoryStockMovement */
    public $resource = InventoryStockMovement::class;

    /**
     * Transform the resource into an array.
     *
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        try{
            return [
                'id' => $this->resource->id,
                'number' => $this->resource->number,
                'old_quantity' => $this->resource->old_quantity,
                'new_quantity' => $this->resource->new_quantity,
                'quantity' => $this->resource->quantity,
                'remarks' => $this->resource->remarks,
                'date' => $this->resource->date,
                'product' => [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                ],
                'location_from' => $this->whenLoaded('locationFrom', [
                    'id' => $this->locationFrom->id,
                    'store' => $this->locationFrom->name,
                ]),
                'section_from' => $this->whenLoaded('sectionFrom', [
                    'id' => $this->sectionFrom?->id,
                    'name' => $this->sectionFrom?->section_name,
                ]),
                'location_to' => $this->whenLoaded('locationTo', [
                    'id' => $this->locationTo->id,
                    'name' => $this->locationTo->name,
                ]),
                'section_to' => $this->whenLoaded('sectionTo', [
                    'id' => $this->sectionTo?->id,
                    'name' => $this->sectionTo?->section_name,
                ]),
                'user' =>  $this->whenLoaded('user',[
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ]),
            ];
        }catch(\Throwable $e) {
            dd($e);
        }
    }
}
