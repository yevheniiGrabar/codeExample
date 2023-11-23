<?php /** @noinspection ALL */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Shipment
 * @package App\Models
 * @property integer $purchase_order_id
 * @property integer $customer_id
 * @property integer $shipping_address_id
 * @property boolean $premium_delivery
 * @property boolean $free_shipping
 * @property integer $item
 * @property Carbon $delivery_date
 * @property boolean $is_shipped
 * @property boolean $is_paid
 * @property boolean $is_ready
 * @property boolean $is_cancelled
 */
class Shipment extends Model
{
    use HasFactory;

    public $fillable = ['*'];

    protected $hidden = ['sale_order_id'];

    protected $casts = ['free_shipping' => 'boolean'];

    /**
     * @return BelongsTo
     */
    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deliveryTerms(): BelongsTo
    {
        return $this->belongsTo(DeliveryTerms::class, 'delivery_terms_id', 'id');
    }
}
