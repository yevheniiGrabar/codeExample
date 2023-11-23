<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\Unit;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UnitResource extends JsonResource
{
    /** @var string|Unit */
    public $resource = Unit::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'code' => $this->resource->barcode,
        ];
    }
}
