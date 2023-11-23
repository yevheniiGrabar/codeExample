<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Line
 * @package App\Models
 * @property integer $purchase_order_id
 * @property integer $invoice_id
 * @property integer $sale_order_id
 * @property integer $product_id
 * @property integer $location_id
 * @property integer $supplier_registration_no
 * @property float $discount
 * @property float $unit_price
 * @property integer $quantity
 * @property float $total
 * @property string $collection_name
 */
class Line extends Model
{
    use HasFactory;

    public $guarded = [];

    /**
     * @return BelongsTo
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function locations(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
