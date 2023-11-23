<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\SubLocation;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SubLocationResource extends JsonResource
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

    /** @var string|SubLocation */
    public $resource = SubLocation::class;


    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        if (self::$mode === 'single') {
            return [
                'id' => $this->resource->id,
                'name' => $this->resource->section_name,
            ];
        } else {
            return [
                'id' => $this->resource->id,
                'name' => $this->resource->section_name,
                'row' => $this->resource->row ?? '',
                'sector' => $this->resource->sector ?? '',
                'height' => $this->resource->shelf_height ?? '',
//            'quantity' => $this->resource->quantity,
            ];
        }
    }
}
