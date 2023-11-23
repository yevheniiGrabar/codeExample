<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Unit
 * @package App\Models
 * @property-read int $product_id
 * @property string $name
 * @property string $barcode
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Unit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
