<?php

namespace App\Models;

use App\Enums\ReceiveStatusEnum;
use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Class PurchaseOrder
 * @package App\Models
 * @property int $id
 * @property int $supplier_id
 * @property Carbon $preferred_delivery_date
 * @property Carbon $purchase_date
 * @property int $our_reference_id
 * @property int $their_reference_id
 * @property int $payment_term_id
 * @property int $delivery_term_id
 * @property int $currency_id
 * @property Carbon $received_at
 * @property float $total
 * @property integer $quantity
 * @property int $receive_id
 * @property int $billing_address_id
 * @property int $delivery_address_id
 * @property int $receive_state
 * @property boolean $is_billing_for_delivery
 * @property string $delivery
 * * @method static filter(mixed $supplierIds, mixed $is_invoiced, mixed $is_received, mixed $dateFrom, mixed $dateTo, mixed $search)
 */
class PurchaseOrder extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $hidden = ['receive_id'];

    protected $casts = ['is_billing_for_delivery' => 'boolean'];


    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function theirReference(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'their_reference_id', 'id');
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
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(BillingAddress::class);
    }

    /**
     * @return BelongsTo
     */
    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(DeliveryAddress::class);
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
     * @return HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'purchase_order_id', 'id');
    }


    public function receives(): HasMany
    {
        return $this->hasMany(Receive::class);
    }

    /**
     * Product filter helper method
     *
     * @param null $query
     * @param null $supplierIds
     * @param mixed $is_invoiced
     * @param null $is_received
     * @param mixed $dateFrom
     * @param mixed $dateTo
     * @param null $search
     * @return object
     */
    public function scopeFilter(
        $query = null,
        $supplierIds = null,
        $is_invoiced = null,
        $is_received = null,
        $dateFrom = null,
        $dateTo = null,
        $search = null,
    ): object
    {

        if ($supplierIds != null && is_array($supplierIds)) {
            $query->whereIn('supplier_id', $supplierIds, 'and');
        } elseif ($supplierIds != null) {
            $query->whereIn('supplier_id', $supplierIds, 'and');
        }

        if ($dateFrom != null) {
            $query->where('purchase_date', '>=', $dateFrom);
        }

        if ($dateTo != null) {
            $query->where('purchase_date', '<=', $dateTo);
        }
        if ($search != null) {
            $query->whereHas('supplier', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
        }

        return $query;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function saveTextDocument(UploadedFile $file)
    {
        $path = Storage::disk('local')->put('images', $file);

        return $this->addMedia($path)->toMediaCollection('images');
    }

    // Functions

    public static function upcomingShipments(): Collection
    {
        return PurchaseOrder::query()
            ->where('receive_state', '!=', ReceiveStatusEnum::RECEIVED)
            ->where('company_id', CurrentCompany::getDefaultCompany()->company_id)
            ->where('preferred_delivery_date', '>', now())
            ->get();
    }
}
