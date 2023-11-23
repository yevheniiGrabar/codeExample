<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->location->id,
            'name' => $this->location->name,
            'quantity' => 1,
            'sections' => $this->whenLoaded(
                'location.sections',
                $this->location->sections->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'name' => $section->section_name,
                        'quantity' => $this->in_stock
                    ];
                })
            ),
        ];
    }
}
