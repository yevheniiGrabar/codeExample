<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $product_id
 * @property int $price_list_id
 * @property float $selling_price
 */
class ProductPriceList extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $table = 'product_price_lists';

    /**
     * @return BelongsToMany
     */
    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_price_lists');
    }

    /**
     * @return BelongsTo
     */
    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class, 'price_list_id', 'id');
    }
}
