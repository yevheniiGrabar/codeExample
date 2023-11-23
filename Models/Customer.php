<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Customer
 * @package App\Models
 * @property integer $id
 * @property string $customer_name
 * @property  string $first_name
 * @property string $last_name
 * @property string $national_id_number
 * @property string $date_of_birth
 * @property string $gender
 * @property integer $customer_code
 * @property string $vat_number
 * @property integer $discount
 * @property string $name
 * @property string $street
 * @property string $street_2
 * @property string $zipcode
 * @property string $city
 * @property string $country
 * @property string $phone
 * @property string $email
 * @property boolean $is_used_for_shipping
 * @property integer $customer_group_id
 * @property integer $tax_id
 * @method static filter(mixed $search)
 */
class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $hidden = [
        'vat_number',
        'ean_number',
        'shipping_address_id',
        'country',
        'customer_group_id',
        'customer_contacts_id'
    ];

    protected $casts = [
        'has_powerOffice' => 'boolean',
        'is_person' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(BillingAddress::class);
    }

    /**
     * @return BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerms::class, 'payment_term_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deliveryTerm(): BelongsTo
    {
        return $this->belongsTo(DeliveryTerms::class, 'delivery_term_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContacts::class);
    }

    /**
     * @return HasMany
     */
    public function deliveryAddresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    //    /**
    //     * @return HasOne
    //     */
    //    public function customerIndividualDiscount(): HasOne
    //    {
    //        return $this->hasOne(CustomerGroup::class, 'customer_group_id', 'id');
    //    }

    /**
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeFilter($query = null, $search = null): mixed
    {
        if ($search != null) {
            $query->where('customer_name', 'like', '%' . $search . '%')
                ->orWhere('customer_code', 'like', '%' . $search . '%');
//                ->orWhereHas('customerContacts', fn($q) => $q->where('contact_email', 'like', '%' . $search . '%'))
//                ->orWhereHas('customerContacts', fn($q) => $q->where('contact_phone', 'like', '%' . $search . '%'))
//                ->orWhereHas('customerContacts', fn($q) => $q->where('contact_name', 'like', '%' . $search . '%'));
        }

        return $query;
    }
}
