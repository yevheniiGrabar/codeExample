<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class InvoiceLine
 * @package App\Models
 * @property integer $product_id
 * @property integer $purchase_order_id
 * @property integer $quantity
 * @property float $total
 */
class InvoiceLine extends Model
{
    use HasFactory;

    public $guarded = [];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'id', 'purchase_order_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}
