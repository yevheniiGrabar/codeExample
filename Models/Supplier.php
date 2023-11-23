<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

/**
 * Class Supplier
 * @package App\Models
 * @property-read  integer $id
 * @property string $name
 * @property string $code
 * @property string $vat
 * @property string $contact_person
 * @property  integer $language_id
 * @property integer $currency_id
 * @property integer $payment_term_id
 * @property integer $tax_id
 * @property string $billing_name
 * @property string $billing_street
 * @property string $billing_street_2
 * @property string $billing_zipcode
 * @property string $billing_city
 * @property integer $country_id
 * @property string $billing_phone
 * @property string $billing_email
 * @property boolean $is_used_for_return
 * @method static filter(mixed $search)
 */
class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['is_used_for_return' => 'boolean'];

    /**
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContacts::class);
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
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function returns(): HasMany
    {
        return $this->hasMany(SupplierReturn::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @param null $query
     * @param null $search
     * @return mixed
     */
    public function scopeFilter($query = null, $search = null): mixed
    {
        if ($search != null) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('vat', 'like', '%' . $search . '%')
                ->orWhere('contact_person', 'like', '%' . $search . '%');
        }
        return $query;
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
    }
}
