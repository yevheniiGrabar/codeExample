<?php

namespace App\Models;

use App\Traits\CurrentCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Collection
 * @package App\Models
 * @property integer $id
 * @property integer $number
 * @property string $name
 * @property string $barcode
 */
class Collection extends Model
{
    use HasFactory, CurrentCompany;

    protected $guarded = [];

    protected $with = ['products'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class,'collection_id', 'id');
    }
}
