<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LocationProduct
 * @package App\Models
 * @property integer $user_id
 * @property integer $currency_id
 * @property boolean $is_base_currency
 */
class UserCurrency extends Model
{
    use HasFactory;

    protected $table = 'user_currency';

    protected $hidden = ['created_at', 'updated_at', 'user_id', 'currency_id'];

    protected $casts = ['is_base_currency' => 'boolean'];

    /**
     * @return BelongsTo
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function currencies(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
