<?php

namespace App\Http\Resources;

use App\Models\SupplierInvoice;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed currency
 * @property mixed ourReference
 * @property mixed purchaseOrder
 * @property mixed supplier
 * @property mixed approvedBy
 * @property mixed billingAddress
 * @property mixed paymentTerms
 * @property mixed deliveryTerm
 */
class SupplierInvoiceResource extends JsonResource
{

    /** @var string|SupplierInvoice */
    public $resource = SupplierInvoice::class;

    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'invoice_number' => $this->resource->invoice_number,
            'invoice_date' => $this->resource->invoice_date,
            'status' => $this->resource->status,
            'total' => $this->resource->total,
//            'purchase_order' => new PurchaseOrderResource($this->purchaseOrder),
//            'supplier' => new SupplierResource($this->supplier),
//            'currency' => new CurrencyResource($this->currency),
//            'our_reference' => new UserResource($this->approvedBy),
//            'billing_address' => new BillingAddressResource($this->billingAddress),
//            'payment_term' => new PaymentTermsResource($this->paymentTerms),
//            'delivery_term' => new DeliveryTermsResource($this->deliveryTerm),
//            'delivery_method' => new DeliveryMethodResource($this->deliveryMethod),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
