<?php


namespace App\Services;


use App\Models\CalendarEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use LogicException;

class CalendarEventService
{
    /**
     * @param string $id
     * @return CalendarEvent
     */
    public function show(string $id): CalendarEvent
    {
        $select = 'id, summary, calendar_id, color, parent_id, description, dt_start, dt_end';

        /** @var CalendarEvent $event */
        $event = CalendarEvent::query()
            ->whereKey($id)
            ->selectRaw($select)
            ->with([
                'subscribers' => function (HasMany $q) use ($select) {
                $q->selectRaw('id, calendar_id, parent_id')
                    ->with([
                        'calendar' => fn(BelongsTo $w) => $w->selectRaw('id, owner_id, owner_type')->with(
                            ['owner'])
                        ]);
                    }
                ]
            )
            ->first();

        if (!$event) {
            throw (new ModelNotFoundException())->setModel('CalendarEvent', $id);
        }

        return $event;
    }

    /**
     * @param array $payload
     *
     * @return CalendarEvent
     */
    public function store(array $payload): CalendarEvent
    {
        $eventData = Arr::except($payload, 'subscribers');

        $this->prepareEvent($eventData, $payload);

        $result = [];

        if (array_key_exists('subscribers', $payload)) {
            $this->fillSubscribers($payload['subscribers'], $eventData, $result);
        }

        array_unshift($result, $eventData);

        CalendarEvent::query()->insert($result);

        return $this->show($eventData['id']);
    }

    /**
     * @param string $id
     *
     * @throws ModelNotFoundException|LogicException
     *
     * @return bool
     */
    public function unsubscribe(string $id): bool
    {
        $event = $this->checkOnExist($id);

        if (!$event->parent_id) {
            throw new LogicException('Can\'t delete parent row');
        }

        return $event->delete();
    }

    /**
     * @param string $id
     *
     * @throws ModelNotFoundException|LogicException
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->checkOnExist($id)->delete();
    }

    /**
     * @param string $id
     * @param array $payload
     *
     * @return bool
     */
    public function update(array $payload, string $id): bool
    {
        $event = $this->checkOnExist($id)
            ->load('subscribers');

        $eventData = Arr::except($payload, 'subscribers');

        $response = $event->update($eventData);

        if ($event->subscribers->count() || array_key_exists('subscribers', $payload)) {
            event(new UpdateCalendarEventSubs($event->subscribers, $id, $eventData, $payload));
        }

        return $response;
    }

    /**
     * @param Collection $subs
     * @param array $claimant
     *
     * @return array
     */
    public function sliceExistSubscribersForDelete(Collection $subs, array $claimant): array
    {
        return $subs->pluck('calendar_id')->diff(collect($claimant))->toArray();
    }
    /**
     * @param Collection $subs
     * @param array $claimant
     *
     * @return array
     */
    public function sliceExistSubscribersForCreate(Collection $subs, array $claimant): array
    {
        return collect($claimant)->diff($subs->pluck('calendar_id'))->toArray();
    }

    /**
     * @param array $subscribers
     * @param array $mainEvent
     * @param array $result
     */
    public function fillSubscribers(array $subscribers, array $mainEvent, array &$result): void
    {
        foreach ($subscribers as $key => &$subscriber) {
            $id = Str::uuid();
            $result[$key] = $mainEvent;
            $result[$key]['id'] = $id;
            ;
            $result[$key]['calendar_id'] = $subscriber;
            $result[$key]['parent_id'] = $mainEvent['id'];
            $result[$key]['icaluid'] = $this->fillIcaluid(['id' => $id]);
        }
    }

    /**
     * @param array $eventData
     * @param array $payload
     */
    public function prepareEvent(array &$eventData, array $payload): void
    {
        $now = now();

        /** @see https://tools.ietf.org/html/rfc2938#section-3.1.2 */
        $eventData['id'] = $payload['id'] ?? Str::uuid();

        $eventData['parent_id'] = null;
        $eventData['created_at'] = $now;
        $eventData['updated_at'] = $now;

        $eventData['icaluid'] = $this->fillIcaluid($eventData);
    }

    /**
     * @param $payload
     *
     * @return string
     */
    private function fillIcaluid(array $payload): string
    {
        $icaliud = sprintf(
            '%s@%s',
            $payload['id'],
            preg_replace('~http:\/\/|https:\/\/~i', '', env('APP_URL', ''))
        );

        return $payload['icaluid'] ?? $icaliud;
    }

    /**
     * @param string $id
     *
     * @return CalendarEvent
     */
    private function checkOnExist(string $id): CalendarEvent
    {
        /** @var CalendarEvent $event */
        if (!$event = CalendarEvent::query()->whereKey($id)->first()) {
            throw (new ModelNotFoundException())->setModel('CalendarEvent', $id);
        }

        return $event;
    }
}
