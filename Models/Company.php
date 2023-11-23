<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Company
 * @package App\Models
 * @property int $id
 * @property string $company_name
 * @property integer $industry_id
 * @property string $country
 * @property string $street
 * @property string $street_2
 * @property string $city
 * @property integer $zipcode
 * @property string $phone_number
 * @property string $email
 * @property string $website
 * @property int $currency_id
 * @property int $language_id
 * @property int $company_billing_id
 * @property Carbon $timezone
 * @property string $company_logo
 */
class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected  $hidden = ['pivot', 'country_id', 'currency_id', 'language_id', 'user_id', 'industry_id', 'company_billing_id'];

    protected $casts = ['is_default' => 'boolean'];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')->withPivot('is_default');
    }

    /**
     * @return BelongsTo
     * @noinspection PhpUnused
     */
    public function companyBilling(): BelongsTo
    {
        return $this->belongsTo(CompanyBilling::class, 'company_billing_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function companyDelivery(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    /**
     * @return HasMany
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function language(): BelongsToMany
    {
        return $this->belongsToMany(Language::class,'company_language');
    }

    /**
     * @return BelongsTo
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'id');
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos');
    }


    public function saveLogo($logo)
    {
        $this->clearMediaCollection('logos');

        $this->addMedia($logo)
            ->toMediaCollection('logos');
    }

    public function paymentTerms(): HasMany {
        return $this->hasMany(PaymentTerms::class);
    }

    public function deliveryTerms(): HasMany {
        return $this->hasMany(DeliveryTerms::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class,'company_language');
    }

    public function deliveryAddresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function saleOrders(): HasMany
    {
        return $this->hasMany(SaleOrder::class);
    }

    public function inventoryStockMovements(): HasMany
    {
        return $this->hasMany(InventoryStockMovement::class);
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function shipmondoAuth(): HasOne
    {
        return $this->hasOne(ShipmondoAuth::class);
    }

    public function powerOfficeAuth(): HasOne
    {
        return $this->hasOne(PowerOfficeAuth::class);
    }
}
