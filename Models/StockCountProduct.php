<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class StockCountProduct
 * @package App\Models
 * @property int $id
 * @property int $stock_count_id
 * @property int $product_id
 * @property int $sub_location_id
 * @property int $counted_quantity
 */
class StockCountProduct extends Model
{
    use HasFactory;

    public $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function subLocation(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class);
    }

    public function stockCount(): BelongsTo
    {
        return $this->belongsTo(StockCount::class);
    }

    public function getInStock()
    {
        $locationId = $this->stockCount->location_id;
        $subLocationId = $this->sub_location_id;

        $locationProduct = LocationProduct::query()
            ->where('location_id', $locationId)
            ->where('sub_location_id', $subLocationId)
            ->firstOrFail();

        return $locationProduct?->in_stock;
    }
}
