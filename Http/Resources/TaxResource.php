<?php

namespace App\Http\Resources;

use App\Models\Tax;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TaxResource extends JsonResource
{
    /**
     * Keeps track of the current mode.
     * @var string
     */
    public static string $mode = 'collection';

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

    /**
     * @var string|Tax
     */
    public $resource = Tax::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        if (self::$mode === 'single') {
            return [
                'id' => $this->resource->id,
                'rate' => $this->resource->rate,
            ];
        } else {
            return [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'rate' => $this->resource->rate,
                'is_sales_tax' => $this->resource->sale_tax,
                'is_purchase_tax' => $this->resource->purchase_tax,
            ];
        }
    }
}
