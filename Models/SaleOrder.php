<?php

/** @noinspection ALL */

namespace App\Models;

use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class SaleOrder
 * @package App\Models
 * @property integer $customer_id
 * @property Carbon $delivery_date,
 * @property Carbon $order_date,
 * @property Carbon $preferred_delivery_date
 * @property boolean $is_billing_for_delivery
 * @property integer $shipment_state
 * @property integer $our_reference_id
 * @property integer $their_reference_id
 * @property integer $currency_id
 * @property integer $payment_term_id
 * @property integer $delivery_term_id
 * @property integer $billing_address_id
 * @property integer $delivery_address_id
 * @property boolean $is_invoiced
 * @property integer $total
 * @property boolean $picking_status
 * @property integer $company_id
 * * @method static filter(mixed $customers, mixed $search, mixed $dates)
 */
class SaleOrder extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = ['is_billing_for_delivery' => 'boolean', 'is_invoiced' => 'boolean'];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function ourReference(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'our_reference_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function theirReference(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'their_reference_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    /**
     * @return BelongsTo
     * @noinspection PhpUnused
     */
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerms::class, 'payment_term_id', 'id');
    }

    /**
     * @return BelongsTo
     * @noinspection PhpUnused
     */
    public function deliveryTerm(): BelongsTo
    {
        return $this->belongsTo(DeliveryTerms::class, 'delivery_term_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(BillingAddress::class, 'billing_address_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(DeliveryAddress::class, 'delivery_address_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(SaleOrderLine::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CustomerInvoice::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Product filter helper method
     *
     * @param  object  $query
     * @param  null  $customers
     * @param  mixed  $is_shipped
     * @param  null  $is_invoiced
     * @param  null  $search
     * @paran null $dates
     * @return Builder|\Illuminate\Database\Eloquent\Collection
     * @noinspection PhpDuplicateSwitchCaseBodyInspection
     */
    public function scopeFilter(
        $query = null,
        $customers = null,
        $dates = null,
        $search = null,
    ): Builder|\Illuminate\Database\Eloquent\Collection {
        if ($search != null) {
            $query->whereHas('customer', fn($q) => $q->where('customer_name', 'like', '%'.$search.'%'));
        }

        if ($customers != null) {
            $query->whereIn('customer_id', $customers, 'and');
        }

        if ($dates != null) {
            $query->whereBetween('order_date', $dates[0], $dates[1]);
        }

        return $query;
    }

    public function scopeCurrentCompany(EloquentBuilder $query)
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        return $query->where('company_id', $currentCompany->company_id);
    }

    public function serialNumbers() {
        return $this->hasMany(PickingSerialNumber::class);
    }

    public function batchNumbers() {
        return $this->hasMany(PickingBatchNumber::class);
    }
}
