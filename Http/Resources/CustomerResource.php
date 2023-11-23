<?php

namespace App\Http\Resources;

use App\Models\Company;
use App\Models\Customer;
use App\Models\DeliveryAddress;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

use function Symfony\Component\Translation\t;

/**
 * @property Company company
 * @property mixed shippingAddress
 * @property mixed customerIndividualDiscount
 * @property mixed $language
 * @property mixed $currency
 * @property mixed $tax
 * @property mixed $supplier
 * @property mixed $billingAddress
 * @property mixed $paymentTerm
 * @property mixed $deliveryTerm
 * @property mixed $customerGroup
 * @property mixed $deliveryAddresses
 * @property mixed $contacts
 */
class CustomerResource extends JsonResource
{
    /** @var string|Customer */
    public $resource = Customer::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->customer_name,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'national_id_number' => $this->resource->national_id_number,
            'date_of_birth' => $this->resource->date_of_birth,
            'gender' => $this->resource->gender,
            'code' => $this->resource->customer_code,
            'vat' => $this->resource->vat_number,
            'tax_rate' => $this->whenLoaded(
                'tax',
                function () {
                    return [
                        'id' => $this->resource->id,
                        'rate' => $this->resource->rate,
                    ];
                }
            ),
            'discount' => $this->resource->discount,
            'contacts' => CustomerContactsResource::collection($this->contacts),
            'billing' => BillingAddressResource::make($this->billingAddress),
            'deliveries' => DeliveryAddressResource::collection($this->deliveryAddresses),
        ];
    }
}
