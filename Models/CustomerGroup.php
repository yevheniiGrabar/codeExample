<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerGroup
 * @package App\Models
 * @property integer $id
 * @property integer $number
 * @property string $name
 * @property float $discount
 * @property boolean $update_discount
 */
class CustomerGroup extends Model
{
    use HasFactory;

    public $fillable = ['*'];

    protected $casts = ['update_discount' => 'boolean'];


}
