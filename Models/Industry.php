<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Industry
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $description
 */
class Industry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
