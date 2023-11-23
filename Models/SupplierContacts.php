<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SupplierContacts
 * @package App\Models
 * @property int $id
 * @property int $supplier_id
 * @property string $name
 * @property string $phone
 * @property string $email
 */
class SupplierContacts extends Model
{
    use HasFactory;

    public $guarded = [];

    public $hidden = ['supplier_id'];

    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
