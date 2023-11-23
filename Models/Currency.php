<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Currency
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $code
 * @property float $currency_rate
 * @property boolean $fixed_exchange_rate
 * @property int $user_id
 * @property string $symbol
 */
class Currency extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $hidden = ['user_id', 'pivot'];

    public $casts = ['fixed_exchange_rate' => 'boolean'];

    /**
     * @return HasMany
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_currency')
            ->withPivot('is_base_currency');
    }

    public static function getNameByCode(string $code): string
    {
        $currencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            "NOK" => "Norwegian krone",
            'PLN' => 'Polish Zloty',
        ];

        return $currencies[$code] ?? '';
    }

    /**
     * @param array $rows
     * @param array $uniqueBy
     * @param array $updateColumns
     * @return bool
     */
    public static function upsert(array $rows, array $uniqueBy, array $updateColumns): bool
    {
        $tableName = DB::getTablePrefix() . (new static)->getTable();

        $columns = array_merge($uniqueBy, $updateColumns);

        $values = [];
        foreach ($rows as $row) {
            $values[] = '(' . implode(
                ',',
                array_map(
                    function ($column) use ($row) {
                        return DB::connection()->getPdo()->quote($row[$column] ?? null);
                    },
                    $columns
                )
            ) . ')';
        }

        $updates = [];
        foreach ($updateColumns as $column) {
            $updates[] = "{$column} = VALUES({$column})";
        }

        $sql = "INSERT INTO {$tableName} (" . implode(',', $columns) . ") VALUES " . implode(
            ',',
            $values
        ) . " ON DUPLICATE KEY UPDATE " . implode(',', $updates);

        return DB::statement($sql);
    }
}
