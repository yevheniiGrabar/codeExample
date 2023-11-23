<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use BinaryCats\Sku\HasSku;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Product
 * @package App\Models
 * @property int $id
 * @property integer $company_id
 * @property integer $category_id
 * @property integer $collection_id
 * @property integer $unit_id
 * @property integer $supplier_id
 * @property integer $location_id
 * @property integer $tax_id
 * @property integer $component_id //reference on products table
 * @property string $name
 * @property string $name_no
 * @property string $name_swe
 * @property string $name_da
 * @property integer $product_code
 * @property integer $barcode
 * @property boolean $has_rfid
 * @property boolean $has_batch_number
 * @property boolean $has_serial_number
 * @property boolean $is_component
 * @property boolean $has_variant
 * @property string $variant_option_1
 * @property string $variant_option_2
 * @property string $variant_option_3
 * @property string $variant_value_1
 * @property string $variant_value_2
 * @property string $variant_value_3
 * @property string $description
 * @property string $description_no
 * @property string $description_swe
 * @property string $description_da
 * @property string $image
 * @property float $cost_price
 * @property float $selling_price
 * @property boolean $has_price_list
 * @property float $selling_price_1
 * @property float $weight
 * @property float $CBM
 * @property float $width
 * @property float $height
 * @property float $length
 * @property int $in_stock
 * @property boolean $has_package_unit
 * @property integer $packing_code
 * @property integer $product_qty
 * @property float $packing_weight
 * @property float $packing_width
 * @property float $packing_height
 * @property float $packing_length
 * @property integer $template_id
 * @property integer $min_inventory_quantity
 * @property integer $min_purchase_quantity
 * @property integer $min_sale_quantity
 * @property boolean $is_deleted
 * @method static Builder notDeleted()
 */
class Product extends Model implements HasMedia
{
    use HasFactory, HasSku, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'variants' => 'array',
        'prices' => 'array',
        'weights_and_sizes' => 'array',
        'in_stock' => 'integer',
        'has_batch_number' => 'boolean',
        'has_serial_number' => 'boolean',
        'has_variant' => 'boolean',
        'is_component' => 'boolean',
        'has_price_list' => 'boolean',
        'has_package_unit' => 'boolean',
        'is_deleted' => 'boolean',
        'has_powerOffice' => 'boolean',
        'has_rfid' => 'boolean',
    ];

    protected $hidden = ['pivot', 'category_id', 'tax_id'];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('withInStockCount', function ($query) {
            $query->withCount('receives as in_stock');
        });
    }


    public function set_is_deleted(int $value): self
    {
        $this->is_deleted = $value;

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return HasOne
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    /**
     * @return HasMany
     */
    public function orderLine(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'product_id', 'id');
    }

    public function getImagePathAttribute()
    {
        $image = Media::query()->where('model_type', Product::class)
            ->where('model_id', $this->id)
            ->first();

        if ($image)
            return '/media/' . $image->id . '/' . $image->file_name;

        return null;
    }

    public function getInStockAttribute()
    {
        return $this->locations()->sum('in_stock');
    }

    /**
     * @return HasMany
     */
    public function saleOrderLine(): HasMany
    {
        return $this->hasMany(SaleOrderLine::class, 'product_id', 'id');
    }

    public function purchaseOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            PurchaseOrder::class,
            OrderLine::class,
            'product_id',
            'id',
            'id',
            'purchase_order_id'
        );
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
    public function priceList(): HasMany
    {
        return $this->hasMany(ProductPriceList::class);
    }

    public function priceHistory() {
        return $this->hasMany(ProductPriceHistory::class);
    }

    /**
     * @return BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function discountGroup(): HasMany
    {
        return $this->hasMany(DiscountGroup::class);
    }

    /**
     * @return HasOne
     */
    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_product', 'product_id', 'location_id')
            ->withPivot(['sub_location_id', 'in_stock', 'min_inventory_quantity', 'min_purchase_quantity', 'min_sale_quantity']);
    }

    public function subLocations(): HasMany
    {
        return $this->hasMany(SubLocation::class, 'product_id', 'id');
    }

    public function translation(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }
  
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile();
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Scope a query to exclude deleted products.
     *
     * @return mixed
     */
    public function scopeFilter($query, $filters): Builder {
        return $query
            ->when(isset($filters['search']) && ($filters['search']), function ($query2) use ($filters) {
                return $query2->where('name', 'LIKE', '%' . $filters['search'] . '%');
            })
            ->when(isset($filters['categories']) && !empty($filters['categories']), function ($query2) use ($filters) {
                return $query2->whereIn('category_id', $filters['categories']);
            })
            ->when(isset($filters['selling_price_range']) && !empty($filters['selling_price_range']), function ($query2) use ($filters) {
                return $query2->whereBetween('cost_price', $filters['selling_price_range']);
            })
            ->when(isset($filters['components']) && !empty($filters['components']), function ($query2) use ($filters) {
                return $query2->where('is_component', 1);
            });
    }

    public function scopeProductOrderBy($query, $orderBy) {
        return $query
            ->when(isset($orderBy['name']) && $orderBy['name'] === 'productName', function ($q) use ($orderBy) {
                return $q->orderBy('name', $orderBy['type']);
            })
            ->when(isset($orderBy['name']) && $orderBy['name'] === 'category', function ($q) use ($orderBy) {
                return $q->withAggregate('category', 'name')->orderBy('category_name', $orderBy['type']);
            })
            ->when(isset($orderBy['name']) && $orderBy['name'] === 'purchasePrice', function ($q) use ($orderBy) {
                return $q->orderBy('cost_price', $orderBy['type']);
            });
    }

    public function receives(): HasMany
    {
        return $this->hasMany(ReceiveHistory::class);
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class);
    }

    public function batchNumbers(): HasMany
    {
        return $this->hasMany(BatchNumber::class);
    }
}
