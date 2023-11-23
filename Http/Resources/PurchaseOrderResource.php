<?php
/** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\ReceiveHistory;
use App\Models\Supplier;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Carbon\Carbon;

use function Ramsey\Collection\firstElement;

/**
 * @property mixed currency
 * @property mixed approvedBy
 * @property mixed delivery
 * @property mixed product
 * @property mixed supplier
 * @property mixed paymentTerms
 */
class PurchaseOrderResource extends JsonResource
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

    /** @var PurchaseOrder|string */
    public $resource = PurchaseOrder::class;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return match (self::$mode) {
            'single' => [
                'id' => $this->resource->id,
                'supplier' => $this->whenLoaded(
                    'supplier',
                    $this->supplier,
                    function () {
                        return [
                            'id' => $this->supplier->id,
                            'name' => $this->supplier->name,
                            'code' => $this->supplier->code,
                        ];
                    }
                ),
                'receive_state' => $this->resource->receive_state,
                'purchase_date' => $this->resource->purchase_date,
//                'total' => [
//                    'value' => $this->resource->total,
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
                                $product,
                                function () {
                                    return [
                                        'id' => $this->product->id,
                                        'name' => $this->product->name,
                                        'code' => $this->product->product_code,
                                        'has_serial_number' => $this->product->has_serial_number,
                                        'has_batch_number' => $this->product->has_batch_nubmer,
                                    ];
                                }
                            ),
                            'quantity' => $this->lines->quantity,
                            'unit_price' => $this->lines->unit_price,
                            'discount' => $this->lines->discount,
                            'tax' => new TaxResource($this->tax),
                        ];
                    }
                ),
//                ],
//                'documents_number' => 1, //todo
            ],
            'details' => [
                'id' => $this->resource->id,
                'supplier' => $this->whenLoaded(
                    'supplier',
                    $this->supplier,
                    function () {
                        return [
                            'id' => $this->supplier->id,
                            'company_name' => $this->supplier->company_name,
                            'code' => $this->supplier->code,
                        ];
                    }
                ),
                'receive_state' => $this->resource->receive_state,
                //'document_number'

                'preferred_delivery_date' => $this->resource->preferred_delivery_date,
                'purchase_date' => $this->resource->purchase_date,
                'receive_date' => $this->resource->received_at,
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
                'payment_terms' => new PaymentTermsResource($this->paymentTerms),
                'delivery_terms' => new DeliveryTermsResource($this->deliveryTerms),
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
                'delivery_address' => new DeliveryAddressResource($this->deliveryAddress),
                'is_billing_for_delivery' => $this->resource->is_billing_for_delivery,
                //@todo
                /*
                 *  documents: {
                    id: number;
                    path: string;
                    name: string;
                     }[]
                 */
                'orders' => $this->whenLoaded('lines', $this->lines, function () {
                        return [
                            'id' => $this->lines->id,
                            'products' => $this->whenLoaded('lines.product', $product,
                                function () {
                                    return [
                                        'id' => $this->product->id,
                                        'name' => $this->product->name,
                                        'code' => $this->product->product_code,
                                        'has_serial_number' => $this->product->has_serial_number,
                                        'has_batch_number' => $this->product->has_batch_nubmer,
                                    ];
                                }
                            ),
                            'quantity' => $this->lines->quantity,
                            'received_quantity' => $this->lines->received_quantity,
                            'unit_price' => $this->lines->unit_price,
                            'discount' => $this->lines->discount,
                            'tax' => new TaxResource($this->tax),

                        ];

                    }
                ),
                'receives' => $this->whenLoaded(
                    'receives',
                    $this->receives,
                    function () {
                        return [
                            'id' => $this->receives->id,
                            'person' => $this->whenLoaded(
                                'receives.user',
                                function () {
                                    return [
                                        'id' => $this->receives->user->id,
                                        'first_name' => $this->receives->user->name,
                                        'last_name' => $this->receives->user->last_name,
                                        'email' => $this->receives->user->email,
                                    ];
                                }
                            ),
                            'is_fully_received' => $this->receives->is_fully_received,
                        ];
                    }
                ),
            ]
        };
    }
}
