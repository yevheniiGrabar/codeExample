<?php

/** @noinspection ALL */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class SupplierInvoiceFactory
 * @package App\Models
 * @property integer $invoice_number
 * @property Carbon $invoice_date
 * @property integer $our_reference_id
 * @property boolean $status
 * @property float $total
 * @property integer $supplier_id
 * @property int $currency_id
 * @property integer $their_reference_id
 * @property integer $billing_address_id
 * @property integer $payment_term_id
 * @property integer $delivery_term_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *  * @method static filter(mixed $supplierId, mixed $date)
 */
class SupplierInvoice extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $hidden = ['supplier_id', 'currency_id', ''];

    protected $casts = ['status' => 'boolean'];

    /**
     * @return HasMany
     */
    public function purchaseOrder(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class)->select(['id', 'total', 'supplier_invoice_id']);
    }

    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
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
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function theirReference(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'their_reference_id', 'supplier_id');
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
    public function paymentTerms(): BelongsTo
    {
        return $this->belongsTo(PaymentTerms::class, 'payment_term_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deliveryTerms(): BelongsTo
    {
        return $this->belongsTo(DeliveryTerms::class, 'delivery_term_id', 'id');
    }

    /**
     * @param object $query
     * @param null $supplier
     * @param null $currency
     * @param null $status
     * @param null $date
     * @return Builder|\Illuminate\Database\Eloquent\Collection
     */
    public static function scopeFilter(
        $query = null,
        $supplierId = null,
        $currencyId = null,
        $status = null,
        $date = null
    ): Builder|\Illuminate\Database\Eloquent\Collection {

        if ($supplierId != null) {
            $query->where('supplier_id', $supplierId, 'and');
        }

        if ($currencyId != null) {
            $query->where('currency_id', $currencyId, 'and');
        }

        if ($status != null) {
            $query->where('status', $status, 'and');
        }

        if ($date != null) {
            $query->where('invoice_date', $date, 'and');
        }

        return $query;
    }

    public function invoiceLine(): HasMany
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id', 'id');
    }
}
