<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PaymentTerms
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property integer $days
 * @property string $type
 * @property int $language_id
 * @property int $company_id
 * @property string $description
 */
class PaymentTerms extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = ['type' => 'boolean'];

    /**
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class,'language_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
