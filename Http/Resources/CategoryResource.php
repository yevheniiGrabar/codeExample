<?php /** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed subcategories
 * @property mixed discountGroup
 */
class CategoryResource extends JsonResource
{
    /** @var Category|string */
    public $resource = Category::class;

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
        ];
    }
}
