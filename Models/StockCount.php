<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class StockCount
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $location_id
 * @property int $sub_location_id
 * @property int $counted_quantity
 * @property int $system_quantity
 * @property int $status
 * @property string $declination_comment
 * @property Carbon $date
 * @method static filter(mixed $search, mixed $locationId, mixed $subLocationId, mixed $date, mixed $workedId)
 */
class StockCount extends Model
{
    use HasFactory;

    public $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function stockCountProduct(): HasMany
    {
        return $this->hasMany(StockCountProduct::class);
    }

    /**
     * @param $query
     * @param $search
     * @param $locationId
     * @param $subLocationId
     * @param $date
     * @param $workerId
     * @return object
     */
    public function scopeFilter(
        $query,
        $search,
        $locationId,
        $subLocationId,
        $date,
        $workerId
    ): object
    {
        if ($search != null) {
            $query->where('created_at', 'like', '%' . $search . '%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('location', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('section', fn($q) => $q->where('section_name', 'like', '%' . $search . '%'))
                ->orWhereHas('product', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
        }

        if ($locationId != null) {
            $query->where('location_id', $locationId);
        }

        if ($subLocationId != null) {
            $query->where('sub_location_id', $subLocationId);
        }

        if ($date != null) {
            $query->where('date', $date);
        }

        if ($workerId != null) {
            $query->where('user_id', $workerId);
        }

        return $query;
    }
}
