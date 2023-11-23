<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserResource extends JsonResource
{
    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'company_billing' => new CompanyBillingResource($this->companyBilling),
            'company_name' => $this->resource->company_name,
            'phone_number' => $this->resource->phone_number,
            'email' => $this->resource->email,
            'website' => $this->resource->website,
            'street' => $this->resource->street,
            'city' => $this->resource->city,
            'country' => $this->resource->country,
            'zipcode' => $this->resource->zipcode,
            'timezone' => $this->resource->timezone,
            'company_logo' => $this->resource->company_logo,
            'is_default' => $this->whenPivotLoaded(
                'company_user',
                function () {
                    return $this->pivot->is_default;
                }
            ),
            'plans' => $this->whenLoaded('plans', PlanResource::collection($this->plans)),
            'currency' => $this->when(
                $this->currency,
                function () {
                    return [
                        'id' => $this->currency->id,
                        'name' => $this->currency->name,
                        'code' => $this->currency->code,
                    ];
                }
            ),
            'language' => $this->when(
                $this->language,
                function () {
                    return [
                        'id' => $this->language->id,
                        'name' => $this->language->name,
                        'code' => $this->language->code,
                    ];
                }
            ),
            'industry' => $this->when(
                $this->industry,
                function () {
                    return [
                        'id' => $this->industry->id,
                        'name' => $this->industry->name,
                    ];
                }
            ),
        ];
    }
}
