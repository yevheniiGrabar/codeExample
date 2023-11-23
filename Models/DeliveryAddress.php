<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class DeliveryAddress
 * @package App\Models
 * @property integer $id
 * @property string $name
 * @property string $street
 * @property string $street_2
 * @property string $postal
 * @property string $email
 * @property string $phone
 * @property string $city
 * @property integer $country_id
 * @property string $contact_person
 * @property boolean $is_primary
 * @property integer $customer_id
 */
class DeliveryAddress extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden =  ['company_id'];

    protected $casts = ['is_primary' => 'boolean'];

    /**
     * @return HasMany
     */
    public function supplier(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class,'company_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
