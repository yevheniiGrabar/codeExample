<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $id
 * @property mixed $shipment_id
 * @property mixed $customer_id
 * @property mixed $return_date
 * @property mixed $status
 */
class SalesReturn extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = ['customer_id', 'shipment_id', 'created_at', 'updated_at'];

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
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id', 'id');
    }
}
