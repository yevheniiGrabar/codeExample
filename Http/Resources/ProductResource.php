<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\ReceiveHistory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed category
 * @property mixed orders
 * @property mixed unit
 * @property mixed company
 * @property mixed components
 * @property mixed supplier
 * @property mixed locations
 * @property mixed tax
 * @property mixed priceList
 * @property mixed sellingPrice
 * @property mixed translation
 * @property mixed currencies
 * @property mixed packingDimension
 * @property mixed template
 * @property mixed pivot
 * @property mixed productPrices
 */
class ProductResource extends JsonResource
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

    /** @var Product|string */
    public $resource = Product::class;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return match (self::$mode) {
            'single' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'code' => $this->resource->product_code,
                'is_component' => $this->resource->is_component,
                'in_stock' => $this->resource->in_stock,
                'image_path' => $this->resource->image_path,
                'prices' => [
                    'purchase_price' => $this->resource->cost_price,
                    'selling_price' => $this->resource->selling_price,
                    'currency' => $this->whenLoaded('currency', function () {
                        return [
                            'id' => $this->resource->currency->id,
                            'name' => $this->resource->currency->name,
                            'code' => $this->resource->currency->code,
                            'symbol' => $this->resource->currency->symbol,
                        ];
                    }),
                ],
                'tax' => $this->when($this->tax, function () {
                    return [
                        'id' => $this->tax->id ?? null,
                        'rate' => $this->tax->rate ?? null,
                    ];
                }),
            ],
            'details' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'code' => $this->resource->product_code,
                'category' => new CategoryResource($this->category),
                'barcode' => $this->resource->barcode,
                'unit' => new UnitResource($this->unit),
                'image_path' => $this->resource->image_path,
                'locations' => $this->when(
                    $this->locations,
                    $this->locations->map(
                        function ($location) {
                            $matchedLocation = $this->resource->locations->whereIn(
                                'id',
                                $this->locations->pluck(
                                    'id'
                                )->toArray()
                            )->where('id', $location->id)->first();
                            if ($matchedLocation) {
                                $locationData = [
                                    'id' => $location->id,
                                    'name' => $location->name,
                                ];

                                if ($location->sections->isNotEmpty()) {
                                    $locationData['sections'] = $location->sections->map(
                                        function ($section) use ($matchedLocation) {
                                            return [
                                                'id' => $section->id,
                                                'name' => $section->section_name,
                                                'inventory' => [
                                                    'location_id' => $matchedLocation->id,
                                                    'in_stock' => $matchedLocation->pivot->in_stock,
                                                    'min_inventory_quantity' => $matchedLocation->pivot->min_inventory_quantity,
                                                    'min_purchase_quantity' => $matchedLocation->pivot->min_purchase_quantity,
                                                    'min_sale_quantity' => $matchedLocation->pivot->min_sale_quantity,
                                                ],
                                            ];
                                        }
                                    );
                                } else {
                                    $locationData['inventory'] = [
                                        'location_id' => $matchedLocation->id,
                                        'in_stock' => $matchedLocation->pivot->in_stock,
                                        'min_inventory_quantity' => $matchedLocation->pivot->min_inventory_quantity,
                                        'min_purchase_quantity' => $matchedLocation->pivot->min_purchase_quantity,
                                        'min_sale_quantity' => $matchedLocation->pivot->min_sale_quantity,
                                    ];
                                }

                                return $locationData;
                            } else {
                                return null;
                            }
                        }
                    )->filter()
                ),
                'supplier' => new SupplierResource($this->supplier),
                'tax' => new TaxResource($this->tax),
                'is_RFID' => $this->resource->has_rfid,
                'is_batch_number' => $this->resource->has_batch_number,
                'is_serial_number' => $this->resource->has_serial_number,
                'is_components' => $this->resource->is_component,
                'template' => new TemplateResource($this->template),

                // 'variants', // needs to create new variants table

                'image' => $this->getFirstMedia('images') ? pathinfo($this->getFirstMedia('images')->getFullUrl(), PATHINFO_BASENAME) : null,
                'description' => $this->resource->description,
                'prices' => [
                    'purchase_price' => $this->resource->cost_price,
                    'selling_price' => $this->resource->selling_price,
                    'currency' => $this->whenLoaded('currency', function () {
                        return [
                            'id' => $this->resource->currency->id,
                            'name' => $this->resource->currency->name,
                            'code' => $this->resource->currency->code,
                            'symbol' => $this->resource->currency->symbol,
                        ];
                    }),
                ],
                'weights_and_sizes' => [
                    'weight' => $this->resource->weight,
                    'CBM' => $this->resource->CBM,
                    'width' => $this->resource->width,
                    'height' => $this->resource->height,
                    'length' => $this->resource->length,
                ],
                'translations' => $this->whenLoaded(
                    'translation',
                    ProductTranslationResource::collection($this->translation)
                ),
                'serial_numbers' => $this->whenLoaded(
                    'serialNumbers',
                    SerialNumberResource::collection($this->serialNumbers)
                ),
                'batch_numbers' => $this->whenLoaded(
                    'batchNumbers',
                    BatchNumberResource::collection($this->batchNumbers)
                )

            ],
            'inventory' => [
                'locations' => $this->when(
                    $this->locations,
                    $this->locations->map(
                        function ($location) {
                            $matchedLocation = $this->resource->locations->whereIn(
                                'id',
                                $this->locations->pluck(
                                    'id'
                                )->toArray()
                            )->where('id', $location->id)->first();
                            if ($matchedLocation) { // added condition
                                return [
                                    'id' => $location->id,
                                    'name' => $location->name,
                                    'inventory' => [
                                        'location_id' => $matchedLocation->id,
                                        'in_stock' => $matchedLocation->pivot->in_stock,
                                        'min_inventory_quantity' => $matchedLocation->pivot->min_inventory_quantity,
                                        'min_purchase_quantity' => $matchedLocation->pivot->min_purchase_quantity,
                                        'min_sale_quantity' => $matchedLocation->pivot->min_sale_quantity,
                                    ],
                                    'sections' => $this->when(
                                        $location->sections,
                                        $location->sections->map(
                                            function ($section) {
                                                return [
                                                    'id' => $section->id,
                                                    'name' => $section->section_name,
                                                ];
                                            }
                                        )
                                    ),
                                ];
                            } else {
                                return null;
                            }
                        }
                    )->filter()
                ),
            ],
            default => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'code' => $this->resource->product_code,
                'image_path' => $this->resource->image_path,
                'category' => new CategoryResource($this->category),
                'prices' => [
                    'purchase_price' => $this->resource->cost_price,
                    'selling_price' => $this->resource->selling_price,
                    'currency' => $this->whenLoaded('currency', function () {
                        return [
                            'id' => $this->resource->currency->id,
                            'name' => $this->resource->currency->name,
                            'code' => $this->resource->currency->code,
                            'symbol' => $this->resource->currency->symbol,
                        ];
                    }),
                ],
                'in_stock' => $this->resource->locations->sum('pivot.in_stock'),
                'is_components' => $this->resource->has_component,
            ],
        };
    }
}
