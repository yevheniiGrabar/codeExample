<?php
/** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\SaleOrder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed customer
 * @property mixed user
 * @property mixed currency
 * @property mixed paymentTerm
 * @property mixed deliveryTerm
 * @property mixed billingAddress
 * @property mixed deliveryAddress
 * @property mixed saleOrderLines
 * @property mixed $supplier
 * @property mixed lines
 * @property mixed theirReference
 * @property mixed users
 */
class SaleOrderResource extends JsonResource
{
    /**
     * Keeps track of the current mode.
     * @var string
     */
    public static string $mode = 'collection';

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


    /** @var SaleOrder|string */
    public $resource = SaleOrder::class;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws \Exception
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return match (self::$mode) {
            'single' => [
                'id' => $this->resource->id,
                'customer' => $this->whenLoaded(
                    'customers',
                    $this->customer,
                    function () {
                        return [
                            'id' => $this->customer->id,
                            'name' => $this->customer->customer_name,
                            'code' => $this->customer->customer_code,
                        ];
                    }
                ),
                'is_invoiced' => $this->resource->is_invoiced,
                'shipment_state' => $this->resource->shipment_state,
                'order_date' => $this->resource->order_date,
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
                'orders' => $this->whenLoaded(
                    'lines',
                    $this->lines,
                    function () {
                        return [
                            'id' => $this->lines->id,
                            'products' => $this->whenLoaded(
                                'lines.product',
                                $this->lines->product,
                                function () {
                                    return [
                                        'id' => $this->lines->product->id,
                                        'name' => $this->lines->product->name,
                                        'code' => $this->lines->product->product_code,
                                    ];
                                }
                            ),
                            'quantity' => $this->lines->quantity,
                            'unit_price' => $this->lines->unit_price,
                            'discount' => $this->lines->discount,
//                            'tax' => new TaxResource($this->tax),
                        ];
                    }
                ),
            ],
            'details' => [
                'id' => $this->resource->id,
                'customer' => $this->whenLoaded(
                    'customer',
                    function () {
                        if ($this->customer->is_person == 1) {
                            return [
                                'id' => $this->customer->id,
                                'name' => $this->customer->first_name . ' ' . $this->customer->last_name,
                                'code' => $this->customer->customer_code,
                                'is_person' => $this->customer->is_person,
                            ];
                        } else {
                            return [
                                'id' => $this->customer->id,
                                'name' => $this->customer->customer_name,
                                'code' => $this->customer->customer_code,
                            ];
                        }
                    }
                ),
                'is_invoiced' => $this->resource->is_invoiced,
                'shipment_state' => $this->resource->shipment_state,
                // 0 | 1 | 2; // 0 -> not shipped, 1 -> ready, 2 -> shipped
                'order_date' => $this->resource->order_date,
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
                'preferred_delivery_date' => $this->resource->preferred_delivery_date,
                'our_reference' => new EmployeeResource($this->ourReference),
                'their_reference' => $this->whenLoaded(
                    'theirReference',
                    $this->theirReference,
                    function () {
                        return [
                            'id' => $this->theirReference->id,
                            'name' => $this->theirReference->contact_person,
                            'code' => $this->theirReference->code,
                        ];
                    }
                ),
                'payment_terms' => new PaymentTermsResource($this->paymentTerm),
                'delivery_terms' => new DeliveryTermsResource($this->deliveryTerm),
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
                'billing_address' => new BillingAddressResource($this->billingAddress),
                'delivery_address' => new BillingAddressResource($this->deliveryAddress),
                'is_billing_for_delivery' => $this->resource->is_billing_for_delivery,
                //@todo
                /*
                documents: {
                 id:number;
                 path: string;
                 name: string;
                } */
                'orders' => $this->whenLoaded(
                    'lines',
                    $this->lines,
                    function () {
                        return [
                            'id' => $this->lines->id,
                            'products' => $this->whenLoaded(
                                'lines.product',
                                $this->lines->product,
                                function () {
                                    return [
                                        'id' => $this->lines->product->id,
                                        'name' => $this->lines->product->name,
                                        'code' => $this->lines->product->product_code,
                                    ];
                                }
                            ),
                            'quantity' => $this->lines->quantity,
                            'unit_price' => $this->lines->unit_price,
                            'discount' => $this->lines->discount,
//                            'tax' => new TaxResource($this->tax),
                        ];
                    }
                ),
                //@ todo
                //shipments data
                //return list data
            ],
        };
    }
}
