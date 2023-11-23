<?php

namespace App\Http\Resources;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\SubLocation;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed product
 * @property mixed location
 * @property mixed category
 */
class InventoryResource extends JsonResource
{
    /** @var string|Inventory */
    public $resource = Product::class;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        try{
            return [
                'id' => $this->id,
                'name' => $this->name,
                'code' => $this->product_code,
                'cost_price' => $this->cost_price,
                'in_stock' => $this->in_stock,
                'category' => $this->whenLoaded('category', [
                    'id' => $this->category?->id,
                    'name' => $this->category?->name,
                    'number' => $this->category?->number,
                ]),
                'product_locations' => $this->whenLoaded('locations', $this->locations->map(function ($location) {
                    return [
                        'location_name' => $location->name,
                        'section_name' => SubLocation::find($location->pivot->sub_location_id)?->section_name,
                        'in_stock' => $location->pivot->in_stock,
                    ];
                })),
            ];
        }catch(\Throwable $e) {
            dd($e);
        }
    }
}
