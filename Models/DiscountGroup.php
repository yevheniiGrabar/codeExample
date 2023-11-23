<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DiscountGroup
 * @package App\Models
 * @property int $id
 * @property int $category_id
 * @property int $number
 * @property string $name
 * @property float $discount
 */
class DiscountGroup extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
