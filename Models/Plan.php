<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Plan
 * @package App\Models
 * @property int $id
 * @property string $stripe_id
 * @property string $identifier
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class);
    }
}
