<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Location
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $contact_name
 * @property string $email
 * @property string $phone_number
 * @property boolean $has_sub_location
 * @property integer $total_quantity
 * @property string $country
 * @property string $city
 * @property string $street
 * @property integer $postal
 * @property integer $company_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 *  * @method static filter(mixed $status, $has_sub_location, Builder|Model|object|null $category)
 */
class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['product_id', 'pivot'];

    public $casts = ['has_sub_location' => 'boolean'];

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'location_product');
    }

    /**
     * @return HasMany
     */
    public function sections(): HasMany
    {
        return $this->hasMany(SubLocation::class, 'location_id', 'id');
    }

    public function productSections(): HasManyThrough
    {
        return $this->hasManyThrough(
            Sublocation::class,
            LocationProduct::class,
            'location_id',
            'id',
            'id',
            'sub_location_id'
        );
    }

    /**
     * @param null $query
     * @param null $has_sub_location
     * @param null $status
     * @return Builder
     */
    public function scope_filter($query = null, $has_sub_location = null, $status = null): Builder
    {
        if ($has_sub_location != null) {
            $query->where('has_sub_location', $has_sub_location, 'and');
        }

        if ($status != 'All' && $status != null) {
            $query->where('status', $status, 'and');
        }

        return $query;
    }

    public function inventories()
    {
        return $this->hasMany(LocationProduct::class);
    }
}
