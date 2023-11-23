<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryTerms
 * @package App\Models
 * @property int $id
 * @property integer $number
 * @property string $name
 * @property string $description
 * @property int $company_id
 */
class DeliveryTerms extends Model
{
    use HasFactory;

    public $guarded = ['id'];
}
