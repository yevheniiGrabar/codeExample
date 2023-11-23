<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BillingAddress
 * @package App\Models
 * @property-read integer $id
 * @property string $name
 * @property string $street
 * @property string $street_2
 * @property string $email
 * @property string $zipcode
 * @property string $city
 * @property integer $country_id
 * @property boolean $is_used_for_shipping
 * @property integer $customer_id
 */
class BillingAddress extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['is_used_for_shipping' => 'boolean', 'is_used_for_return' => 'boolean'];

//    protected $hidden = ['created_at', 'updated_at', 'is_used_for_shipping', 'country_id'];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
