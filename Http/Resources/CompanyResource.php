<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed plans
 * @property mixed users
 * @property mixed companyBilling
 * @property mixed pivot
 * @property mixed language
 * @property mixed currency
 * @property mixed industry
 * @property mixed companyDelivery
 */
class CompanyResource extends JsonResource
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

    /** @var Company|string */
    public $resource = Company::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return match (self::$mode) {
            'single' => [
                'id' => $this->resource->id,
                'name' => $this->resource->company_name,
                'is_default' => $this->whenPivotLoaded(
                    'company_user',
                    function () {
                        return $this->pivot->is_default;
                    }
                ),
            ],
            'details' => [
                'id' => $this->resource->id,
                'name' => $this->resource->company_name,
                'is_default' => $this->whenPivotLoaded(
                    'company_user',
                    function () {
                        return $this->pivot->is_default;
                    }
                ),
                'industry' => new IndustryResource($this->industry),
                'country' => $this->whenLoaded('country', function () {
                    return [
                        'id' => $this->resource->country->id,
                        'name' => $this->resource->country->name,
                        'code' => $this->resource->country->code,
                    ];
                }),
                'street' => $this->resource->street,
                'city' => $this->resource->city,
                'zipcode' => $this->resource->zipcode,
                'phone' => $this->resource->phone_number,
                'email' => $this->resource->email,
                'website' => $this->resource->website,
                'currency' => $this->whenLoaded(
                    'currency',
                    $this->currency,
                    function () {
                        return [
                            'id' => $this->currency->id,
                            'name' => $this->currency->name,
                            'code' => $this->currency->code,
                        ];
                    }
                ),
                'language' => $this->whenLoaded(
                    'language',
                    $this->language,
                    function () {
                        return [
                            'id' => $this->language->id,
                            'name' => $this->language->name,
                            'code' => $this->language->code,
                        ];
                    }
                ),
                'logo' => str_replace('http://127.0.0.1', '', $this->resource->getFirstMediaUrl('logos')),

                'billing_address' => $this->whenLoaded(
                    'companyBilling',
                    $this->companyBilling,
                    function () {
                        return [
                            'id' => $this->companyBilling->id,
                            'name' => $this->companyBilling->name,
                            'street' => $this->companyBilling->billing_street,
                            'street_2' => $this->companyBilling->billing_street_2,
                            'zipcode' => $this->companyBilling->billing_postal,
                            'city' => $this->companyBilling->billing_city,
                            'country' => $this->whenLoaded('country', function () {
                            return [
                                'id' => $this->resource->id,
                                'name' => $this->resource->name,
                                'code' => $this->resource->code,
                            ];
                        }),
                            'email' => $this->companyBilling->billing_email,
                            'phone' => $this->companyBilling->billing_phone,
                            'contact_name' => $this->companyBilling->contact_name,
                            'is_used_for_delivery' => $this->companyBilling->is_used_for_delivery
                        ];
                    }
                ),

                'deliveries' => $this->whenLoaded(
                    'companyDelivery',
                    $this->companyDelivery,
                    function () {
                        return [
                            'id' => $this->companyDelivery->id,
                            'name' => $this->companyDelivery->name,
                            'country' => $this->whenLoaded('country', function () {
                                return [
                                    'id' => $this->resource->id,
                                    'name' => $this->resource->name,
                                    'code' => $this->resource->code,
                                ];
                            }),
                            'street' => $this->companyDelivery->steet,
                            'street_2' => $this->companyDelivery->street_2,
                            'zipcode' => $this->companyDelivery->postal,
                            'city' => $this->companyDelivery->city,
                            'email' => $this->companyDelivery->email,
                            'phone' => $this->companyDelivery->phone,
                            'contact_person' => $this->companyDelivery->contact_person,
                        ];
                    }
                ),
            ],
            default => [
                'id' => $this->resource->id,
                'name' => $this->resource->company_name,
            ]
        };
    }
}
