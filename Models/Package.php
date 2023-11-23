<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Package
 * @package App\Models
 * @property integer $id
 * @property integer $id_number
 * @property string $name
 * @property float $width
 * @property float $length
 * @property float $height
 */
class Package extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}
