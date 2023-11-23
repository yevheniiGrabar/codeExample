<?php /** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\CompanyBilling;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CompanyBillingResource extends JsonResource
{
    /** @var CompanyBilling|string */
    public $resource = CompanyBilling::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->billing_email,
            'street' => $this->resource->billing_street,
            'street_2' => $this->resource->billing_street_2,
            'city' => $this->resource->billing_city,
            'zipcode' => $this->resource->billing_postal,
            'country' => CountryResource::make($this->resource->country),
            'contact_name' => $this->resource->contact_name,
            'phone' => $this->resource->billing_phone,
            'is_used_for_delivery' => $this->resource->is_used_for_delivery
        ];
    }
}
