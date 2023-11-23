<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class BillOfMaterial
 * @package App\Models
 * @property integer $id
 * @property string $name
 * @property integer $product_id
 * @property integer $company_id
 */
class BillOfMaterial extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['product_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
