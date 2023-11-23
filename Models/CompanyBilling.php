<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CompanyBilling
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property integer $country_id
 * @property string $billing_city
 * @property string $billing_street
 * @property string $billing_street_2
 * @property string $billing_postal
 * @property string $billing_email
 * @property string $billing_phone
 * @property string $contact_name
 * @property bool $is_used_for_delivery
 */
class CompanyBilling extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_used_for_delivery' => 'boolean'
    ];

    public function company(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
