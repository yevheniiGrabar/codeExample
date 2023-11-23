<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderLine
 * @package App\Models
 * @property integer $id
 * @property integer $product_id
 * @property float $discount
 * @property float $unit_price
 * @property integer $quantity
 * @property string $collection_name
 * @property integer $purchase_order_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OrderLine extends Model
{
    use HasFactory;

    public $guarded = [];

    public $hidden = [ 'purchase_order_id','tax_id'];

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
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'id');
    }

    public function receiveHistory(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReceiveHistory::class);
    }
}
