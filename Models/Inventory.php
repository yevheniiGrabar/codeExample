<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Inventory
 * @package App\Models
 * @property int $id
 * @property int $product_id
 * @property int $location_id
 * @property int $category_id
 * @property float $cost_price
 * @property int $in_stock
 */
class Inventory extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class,'location_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
