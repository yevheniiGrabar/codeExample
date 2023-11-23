<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class PriceList
 * @package App\Models
 * @property integer $id
 * @property string $name
 * @property integer $code
 * @property integer $currency_id
 */
class PriceList extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['pivot'];

    /**
     * @return HasOne
     */
    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_price_list');
    }

    /**
     * @return HasMany
     */
    public function priceList(): HasMany
    {
        return $this->hasMany(ProductPriceList::class);
    }
}
