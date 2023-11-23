<?php

namespace App\Models;

use App\Conracts\ICalendar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
* @property string $id           Identifier of the event.
 * @property string $summary      Title of the event. https://tools.ietf.org/html/rfc5545#section-3.8.1.12
 * @property string $calendar_id  Identifier of the calendar.
 * @property string $description  Description of the event.
 * @property string $status       Status of the event.
 * @property string $sequence     Sequence number as per iCalendar.
 * @property string $transparency Whether the event blocks time on the calendar.
 * @property string $visibility   Visibility of the event.
 * @property string $icaluid      Event unique identifier as defined in RFC5545.
 * @property Carbon $dt_start     Date of start event.
 * @property Carbon $dt_end       Date of end event.
 * @property string $color        Color event.
 * @property string $parent_id    Parent id event.
 * @property bool   $all_day      All day event.
 * @property Carbon $created_at   Date of creation.
 * @property Carbon $updated_at   Date of updating.
 */
class CalendarEvent extends Model implements ICalendar
{
    use HasFactory;

    /** @var array $guarded */
    protected $guarded = [];

    /** @var string $keyType */
    protected $keyType = 'string';

    /** @var bool $incrementing */
    public $incrementing = false;

    /** @var string $table */
    protected $table = 'calendar_events';

    /** @var array $casts */
    protected $casts = [
        'dt_start' => 'date',
        'dt_end' => 'date'
    ];

    /**
     * Get calendar of event
     *
     * @return BelongsTo
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id', 'id');
    }

    /**
     * Get calendar of event
     *
     * @return HasMany
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
