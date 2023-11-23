<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Receive
 * @package App\Models
 * @property int id
 * @property int $supplier_id
 * @property Carbon $receive_date
 * @property integer $receive_quantity
 * @property integer $total_quantity
 * @property boolean $is_fully_received
 * * @method static filter(mixed $supplierId, mixed $receiveDate)
 */
class Receive extends Model
{
    use HasFactory;

    public $guarded = [];


    public $hidden = ['supplier_id', 'user_id', 'company_id', 'created_at', 'updated_at', 'purchase_order_id'];

    protected $casts = ['is_fully_received' => 'boolean'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function receiveHistory(): HasMany
    {
        return $this->hasMany(ReceiveHistory::class);
    }

    /**
     * @return BelongsTo
     */
    public function subLocation(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class, 'sub_location_id', 'id');
    }

    /**
     * Product filter helper method
     *
     * @param object $query
     * @param null $supplierId
     * @param mixed $receiveDate
     * @return object
     */
    public function scopeFilter($query = null, $supplierId = null, $receiveDate = null): object
    {

        if ($supplierId != null) {
            $query->where('supplier_id', $supplierId, 'and');
        }

        if ($receiveDate != null) {
            $query->where('receive_date', $receiveDate, 'and');
        }

        return $query;
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(ReceiveSerialNumber::class);
    }

    public function batchNumbers(): HasMany
    {
        return $this->hasMany(ReceiveBatchNumber::class);
    }
}
