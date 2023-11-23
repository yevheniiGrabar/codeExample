<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class LocationProduct
 * @package App\Models
 * @property integer $location_id
 * @property integer $sub_location_id
 * @property integer $product_id
 * @property integer $in_stock
 * @property integer $min_inventory_quantity
 * @property integer $min_purchase_quantity
 * @property integer $min_sale_quantity
 */
class LocationProduct extends Model
{
    use HasFactory;

    protected $table = 'location_product';

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'product_id', 'location_id', 'sub_location_id'];


    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function locations(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(SubLocation::class, 'location_product');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class, 'sub_location_id', 'id');
    }
}
