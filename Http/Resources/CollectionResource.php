<?php /** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed products
 */
class CollectionResource extends JsonResource
{
    /** @var string|Collection */
    public $resource = Collection::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'name' => $this->resource->name,
            'barcode' => $this->resource->barcode,
            'products' => $this->whenLoaded('products', ProductMutateResource::collection($this->products)),
       ];
    }
}
