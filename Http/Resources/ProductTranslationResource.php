<?php

namespace App\Http\Resources;

use App\Models\ProductTranslation;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed language
 */
class ProductTranslationResource extends JsonResource
{
    /** @var ProductTranslation|string */
    public $resource = ProductTranslation::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null):array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'language' => new LanguageResource($this->language),
            'translated_name' => $this->resource->translated_name,
            'translated_description' => $this->resource->translated_description
        ];
    }
}
