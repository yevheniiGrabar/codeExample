<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\DiscountGroup;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed category
 */
class DiscountGroupResource extends JsonResource
{
    /**
     * @var string|DiscountGroup
     */
    public $resource = DiscountGroup::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'name' => $this->resource->name,
            'discount' => $this->resource->discount,
        ];
    }
}
