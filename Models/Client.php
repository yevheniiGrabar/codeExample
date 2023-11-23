<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Client
 * @package App\Models
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Client extends Model
{
    use HasFactory;

    protected $fillable = ['*'];

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(ShippingAddress::class,'shipping_address_id', 'id');
    }
}
