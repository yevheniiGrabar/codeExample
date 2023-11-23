<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed $language;
 * @property mixed $country;
 */
class MeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'data' => $this->removeMissingValues(
                [
                    'id' => $this->resource->id,
                    'name' => $this->resource->name,
                    'last_name' => $this->resource->last_name,
                    'email' => $this->resource->email,
                    'phone' => $this->resource->phone,
                    'country' => new CountryResource($this->country),
                    'language' => new LanguageResource($this->language),
                    'created_at' => $this->resource->created_at,
                    'updated_at' => $this->resource->updated_at,
                    $this->mergeWhen(!empty($this->additional), $this->additional),
                ]
            ),
        ];
    }
}
