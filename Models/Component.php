<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Component
 * @package App\Models
 * @property integer $product_id
 * @property integer $bill_of_material_id
 * @property integer $quantity
 * @property float $unit_price
 */
class Component extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'bill_of_material_id',
        'product_id',
        'created_at',
        'updated_at',
        'unit_price',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class,'bill_of_material_id', 'id');
    }
}
