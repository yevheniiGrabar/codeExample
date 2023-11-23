<?php /** @noinspection ALL */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ShippingAddress
 * @package App\Models
 * @property integer $client_id
 * @property integer $company_id
 * @property string $name
 * @property string $street
 * @property string $street_2
 * @property string $city
 * @property string $zipcode
 * @property string $country
 * @property string $contact_person
 * @property string $phone
 * @property string $email
 * @property Carbon $created_at
 */
class ShippingAddress extends Model
{
    use HasFactory;

    public $fillable = ['*'];

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
