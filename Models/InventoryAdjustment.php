<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventoryAdjustment
 * @package App\Models
 * @property int $id
 * @property integer $product_id
 * @property integer $location_id
 * @property integer $sub_location_id
 * @property Carbon $date
 * @property integer $user_id
 * @property boolean $adjustment_type
 * @property integer $old_quantity
 * @property integer $actual_quantity
 * @property float $old_cost_price
 * @property float $actual_cost_price
 * @property string $remarks
 * @property integer $company_id
 * @method static filter(mixed $search, mixed $products,  mixed $location, mixed $remarks, mixed $date) //mixed $users,
 */
class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $hidden = ['product_id', 'user_id', 'location_id', 'sub_location_id'];


    public $guarded = [];

    public $casts = ['adjustment_type' => 'boolean'];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function sections(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class, 'sub_location_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param null $query
     * @param null $search
     * @param null $products
     * @param null $users
     * @param null $location
     * @param null $remarks
     * @param null $adjustments
     * @param null $date
     * @return mixed
     */
    public function scopeFilter(
        $query = null,
        $search = null,
        $products = null,
        $users = null,
        $location = null,
        $remarks = null,
        $adjustments = null,
        $date = null
    ): mixed {
        if ($search != null) {
            $query->where('remarks', 'like', '%' . $search . '%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('location', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('product', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
        }

        if ($products != null) {
            $query->whereIn('product_id', $products, 'and');
        }

//        if ($users != null) {
//            $query->whereIn('user_id', $users, 'and');
//        }

        if ($location != null) {
            $query->where('location_id', $location->store, 'and');
            if (isset($location->section)) {
                $query->where('sub_location_id', $location->section, 'and');
            }
        }
        if ($remarks != null) {
            if ($remarks == 1) {
                $query->where('remarks', '!=', '', 'and');
            } elseif ($remarks == 0) {
                $query->where(function ($query) {
                    $query->where('remarks', '=', '')
                        ->orWhereNull('remarks');
                });
            }
        }
        if ($date != null) {
            $query->where('date', $date, 'and');
        }

        return  $query;
    }
}
