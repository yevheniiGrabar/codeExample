<?php

namespace App\Http\Controllers;

use App\Enums\Permissions\Access;
use App\Http\Requests\CalendarEventStoreRequest;
use App\Models\CalendarEvent;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @group Calendar Events
 *
 * Endpoints for managing calendar events
 */
class CalendarEventController extends Controller
{
    /**
     * List
     *
     * Returns list of available calendar events
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize(Access::VIEW_CALENDARS_LIST, CalendarEvent::class);

        $calendarEvent = CalendarEvent::query()->get(['title', 'start']);

        return new JsonResponse([$calendarEvent]);
    }

    /**
     * Create
     *
     * Store a newly created calendar event in storage.
     * @authenticated
     * @param CalendarEventStoreRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(CalendarEventStoreRequest $request): JsonResponse
    {
        $this->authorize(Access::CREATE_NEW_CALENDAR, CalendarEvent::class);

        return new JsonResponse(
            $calendarEvent = CalendarEvent::query()->create($request->validated()),
            Response::HTTP_OK
        );
    }

    /**
     * Show
     *
     * Display the specified calendar event.
     * @authenticated
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $this->authorize(Access::VIEW_CURRENT_CALENDAR, CalendarEvent::class);

        $calendarEvent = CalendarEvent::query()->find($id);

        return new JsonResponse($calendarEvent);
    }

    /**
     * Edit
     *
     * Update the specified calendar event in storage.
     * @authenticated
     * @param Request $request
     * @param CalendarEvent $calendarEvent
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(Request $request, CalendarEvent $calendarEvent): JsonResponse
    {
        //add access constant

        $calendarEvent->updateOrFail($request->all());

        return new JsonResponse($calendarEvent);
    }

    /**
     * Delete
     *
     * Remove the specified calendar event from storage.
     * @authenticated
     * @param CalendarEvent $calendarEvent
     * @return JsonResponse
     */
    public function destroy(CalendarEvent $calendarEvent): JsonResponse
    {
        //add delete constant
        $calendarEvent->delete();

        return new JsonResponse([]);
    }
}
