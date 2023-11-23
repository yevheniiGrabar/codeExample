<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'employee_number' => $this->resource->employee_number,
            'name' => $this->resource->name,
            'job_title' => $this->resource->job_title,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'company' => new CompanyResource($this->company),
            'language' =>  new LanguageResource($this->language),
        ];
    }
}
