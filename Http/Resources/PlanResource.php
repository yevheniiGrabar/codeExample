<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\Plan;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PlanResource extends JsonResource
{
    /** @var Plan|string */
    public $resource = Plan::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
