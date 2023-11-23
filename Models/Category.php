<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Category
 * @package App\Models
 * @property int $id
 * @property  int $number
 * @property string name
 * @property int $company_id
 *
 */
class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function discountGroup(): HasOne
    {
        return $this->hasOne(DiscountGroup::class);
    }
}
