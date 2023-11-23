<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventoryStockMovement
 * @package App\Models
 * @property integer $id
 * @property integer $number
 * @property integer $old_quantity
 * @property integer $new_quantity
 * @property integer $product_id
 * @property integer $from_location_id
 * @property integer $from_section_id
 * @property integer $to_location_id
 * @property integer $to_section_id
 * @property integer $user_id
 * @property integer $quantity
 * @property string $remarks
 * @property Carbon $date
 * @method static filter(mixed $search, mixed $products, mixed $users, mixed $location_from, mixed $location_to, mixed $remarks, mixed $dateFrom, mixed $dateTo)
 *
 */
class InventoryStockMovement extends Model
{
    use HasFactory;

    public $guarded = [];

    public $hidden = [
        'product_id',
        'from_location_id',
        'from_section_id',
        'to_location_id',
        'to_section_id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function locationFrom(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id', 'id');
    }

    public function sectionFrom(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class, 'from_section_id', 'id');
    }

    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id', 'id');
    }

    public function sectionTo(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class, 'to_section_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @param null $query
     * @param null $search
     * @param null $products
     * @param null $users
     * @param null $location_from
     * @param null $location_to
     * @param null $remarks
     * @param mixed $dateFrom
     * @param mixed $dateTo
     * @return Builder
     */
    public function scopeFilter(
        $query = null,
        $search = null,
        $products = null,
        $users = null,
        $location_from = null,
        $location_to = null,
        $remarks = null,
        $dateFrom = null,
        $dateTo = null,
    ): Builder
    {
        if ($search != null) {
            $query->where('remarks', 'like', '%' . $search . '%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('locationFrom', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('locationTo', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('sectionFrom', fn($q) => $q->where('section_name', 'like', '%' . $search . '%'))
                ->orWhereHas('sectionTo', fn($q) => $q->where('section_name', 'like', '%' . $search . '%'))
                ->orWhereHas('product', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
        }
        if ($products != null) {
            $query->whereIn('product_id', $products, 'and');
        }
        if ($users != null) {
            $query->whereIn('user_id', $users, 'and');
        }
        if ($location_from != null) {
            $query->where('from_location_id', $location_from->store, 'and');
            if (isset($location_from->section)) {
                $query->where('from_section_id', $location_from->section, 'and');
            }
        }
        if ($location_to != null) {
            $query->where('to_location_id', $location_to->store, 'and');
            if (isset($location_to->section)) {
                $query->where('to_section_id', $location_to->section, 'and');
            }
        }
        if ($remarks !== null) {
            $query->where(function ($query) use ($remarks) {
                if ($remarks == 1) {
                    $query->where('remarks', '!=', '');
                } elseif ($remarks == 0) {
                    $query->where(function ($query) {
                        $query->where('remarks', '=', '')
                            ->orWhereNull('remarks');
                    });
                }
            });
        }

        if ($dateFrom != null) {

            $query->where('date', '>=', $dateFrom);
        }

        if ($dateTo != null) {
            $query->where('date', '<=', $dateTo);
        }
        return $query;
    }
}
