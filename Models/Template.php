<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Template
 * @package App\Models
 * @property string $name
 * @property array $disabled_fields
 */
class Template extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $hidden = ['created_at', 'updated_at'];

    public $casts =  ['disabled_fields' => 'array'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
