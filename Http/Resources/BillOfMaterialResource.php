<?php

namespace App\Http\Resources;

use App\Models\ReceiveHistory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class BillOfMaterialResource extends JsonResource
{
    /** @var mixed */
    public $components;

    /** @var mixed */
    public $product;

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


    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        $resourceData = [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'product' => [
                'id' => $this->resource->product->id,
                'name' => $this->resource->product->name,
                'code' => $this->resource->product->product_code,
                'unit_price' => $this->resource->product->product_unit_price,
                'category' => $this->whenLoaded(
                    'category',
                    [
                        'id' => $this->resource->product->category->id,
                        'name' => $this->resource->product->category->name,
                    ]
                ),
            ],
            'components' => $this->resource->components->map(
                function ($component) {
                    $receivedQuantity = ReceiveHistory::where('product_id', $component->product->id)->count();

                    return [
                        'id' => $component->id,
                        'product' => [
                            'id' => $component->product->id,
                            'name' => $component->product->name,
                            'unit_price' => $component->product->unit_price,
                            'in_stock' => $receivedQuantity, // Add in_stock here
                        ],
                        'quantity' => $component->quantity,
                    ];
                }
            ),
        ];

        if ($this->resource->product->tax) {
            $resourceData['product']['tax'] = [
                'id' => $this->resource->product->tax->id,
                'name' => $this->resource->product->tax->name,
            ];
        }

        if ($this->resource->product_id) {
            $receivedQuantity = ReceiveHistory::where('product_id', $this->resource->product_id)->count();
            $resourceData['product']['in_stock'] = $receivedQuantity;
        }
        return $resourceData;
    }
}
