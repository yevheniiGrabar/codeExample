<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed sections
 * @property mixed inventories
 */
class LocationResource extends JsonResource
{
    /**
     * Keeps track of the current mode.
     * @var string
     */
    public static $mode = 'collection';

    /**
     * Set the current mode for this resource.
     * @param $mode
     * @return string
     */
    public static function setMode($mode): string
    {
        self::$mode = $mode;
        return __CLASS__;
    }

    /** @var Location|string */
    public $resource = Location::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return match (self::$mode) {
            'single' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'sections' => $this->when(
                    $this->resource->has_sub_location != false,
                    $this->sections->map(
                        function ($section) {
                            return [
                                'id' => $section->id,
                                'name' => $section->section_name
                            ];
                        }
                    )
                ),
            ],
            'details' => [
                'id' => $this->resource->id,
                'store' => $this->resource->name,
                'country' => $this->resource->country,
                'city' => $this->resource->city,
                'sections' => $this->when(
                    $this->resource->has_sub_location != false,
                    $this->sections->map(
                        function ($section) {
                            return [
                                'id' => $section->id,
                                'name' => $section->section_name,
                                'sector' => $section->sector,
                                'row' => $section->row,
                                'shelf_height' => $section->shelf_height,
                            ];
                        }
                    )
                ),
            ],
            default => [
                'id' => $this->resource->id,
                'store' => $this->resource->name,
                'country' => $this->resource->country,
                'city' => $this->resource->city,
                'sections' => $this->when(
                    $this->resource->has_sub_location != false,
                    $this->sections->map(
                        function ($section) {
                            return [
                                'id' => $section->id,
                                'name' => $section->section_name,
                                'sector' => $section->sector,
                                'row' => $section->row,
                                'shelf_height' => $section->shelf_height,
                            ];
                        }
                    )
                ),
            ]
        };
    }
}
