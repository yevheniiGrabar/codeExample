<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TemplateResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     * @noinspection PhpMissingParamTypeInspection
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
//        $data = json_decode($this->resource->disabled_fields);
//        $data = json_decode($data);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'disabled_fields' =>  json_decode($this->resource->disabled_fields, 'true'),
        ];
    }
}
