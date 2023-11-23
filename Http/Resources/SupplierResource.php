<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\Supplier;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed language
 * @property mixed currency
 * @property mixed billing
 * @property mixed contacts
 * @property mixed paymentTerm
 * @property mixed tax
 * @property mixed returns
 */
class SupplierResource extends JsonResource
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


    /** @var Supplier|string */
    public $resource = Supplier::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return match (self::$mode) {
            'slim' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'code' => $this->resource->code,
            ],
            'details' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'code' => $this->resource->code,
                'vat' => $this->resource->vat,
                'currency' => new CurrencyResource($this->currency),
                'tax_rate' => $this->whenLoaded(
                    'tax',
                    $this->tax,
                    function () {
                        return [
                            'id' => $this->tax->id,
                            'rate' => $this->tax->rate,
                        ];
                    }
                ),
                'payment_terms' => new PaymentTermsResource($this->paymentTerm),
                'contacts' => $this->whenLoaded(
                    'contacts',
                    $this->contacts,
                    function () {
                        return [
                            'id' => $this->contacts->id,
                            'name' => $this->contacts->name,
                            'phone' => $this->contacts->phone,
                            'email' => $this->contacts->email,
                        ];
                    }
                ),
                'billing' => [
                    'name' => $this->resource->billing_name,
                    'street' => $this->resource->billing_street,
                    'street_2' => $this->resource->billing_street_2,
                    'zipcode' => $this->resource->billing_zipcode,
                    'city' => $this->resource->billing_city,
                    'country' => $this->whenLoaded(
                        'country',
                        function () {
                            return [
                                'id' => $this->resource->country->id,
                                'name' => $this->resource->country->name,
                            ];
                        }
                    ),
                    'phone' => $this->resource->billing_phone,
                    'email' => $this->resource->billing_email,
                    'is_used_for_return' => $this->resource->is_used_for_return,
                ],
                'returns' => $this->whenLoaded(
                    'returns',
                    $this->returns,
                    function () {
                        return [
                            'id' => $this->returns->id,
                            'name' => $this->returns->name,
                            'street' => $this->returns->street,
                            'street_2' => $this->returns->street_2,
                            'zipcode' => $this->returns->zipcode,
                            'city' => $this->returns->city,
                            'country' => $this->whenLoaded(
                                'country',
                                function () {
                                    return [
                                        'id' => $this->returns->contry->id,
                                        'name' => $this->resource->name,
                                    ];
                                }
                            ),
                            'contact_person' => $this->returns->contact_person,
                            'phone' => $this->returns->phone,
                            'email' => $this->returns->email,
                            'is_primary' => $this->returns->is_primary,
                        ];
                    }
                )
            ],
            default => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'code' => $this->resource->code,
                'contacts' => $this->whenLoaded(
                    'contacts',
                    $this->contacts,
                    function () {
                        return [
                            'id' => $this->contacts->id,
                            'name' => $this->contacts->name,
                            'phone' => $this->contacts->phone,
                            'email' => $this->contacts->email,
                        ];
                    }
                )
            ]
        };
    }
}
