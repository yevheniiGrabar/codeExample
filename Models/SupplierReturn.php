<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SupplierReturn
 * @package App\Models
 * @property string $name
 * @property string $street
 * @property string $street_2
 * @property string $zipcode
 * @property string $city
 * @property integer $country_id
 * @property string $phone
 * @property string $email
 * @property string $contact_person
 * @protected boolean $is_primary
 */
class SupplierReturn extends Model
{
    use HasFactory;

    protected $guarded= [];

    protected  $hidden = ['supplier_id','country_id'];

    protected $casts = ['is_primary' => 'boolean'];


    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
