<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property mixed|null $collection_name
 * @property mixed $discount
 * @property mixed $unit_price
 * @property mixed $supplier_registration_no
 * @property mixed $quantity
 * @property mixed $product_id
 * @property mixed $location_id
 * @property mixed $sale_order_id
 * @property mixed $tax_id
 */
class SaleOrderLine extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'sale_order_id','product_id', 'tax_id'
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id', 'id');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'id');
    }
}
