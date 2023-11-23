<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tax
 * @package App\Models
 * @property integer $id
 * @property integer $amount
 * @property string $name
 * @property float $rate
 * @property boolean $sale_tax
 * @property boolean $purchase_tax
 */
class Tax extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

     protected $casts = [
         'sale_tax' => 'boolean',
         'purchase_tax' => 'boolean'
     ];

}
