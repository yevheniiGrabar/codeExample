<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Class SubLocation
 * @package App\Models
 * @property integer $id
 * @property string $section_name
 * @property string $row
 * @property string $sector
 * @property string $shelf_height
 * @property integer $quantity
 * @property int $location_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SubLocation extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $hidden = ['location_id'];

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
