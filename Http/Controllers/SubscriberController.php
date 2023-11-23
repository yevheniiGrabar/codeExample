<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriberResource;
use App\Models\Subscriber;
use App\Services\JsonResponseDataTransform;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Subscriber
 *
 * Endpoints for managing subscribers
 */
class SubscriberController extends Controller
{

    /**@var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available subscribers
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $subscribers = Subscriber::query()->orderBy('id', 'desc')->get();

        return $this->dataTransform->conditionalResponse($request,SubscriberResource::collection($subscribers));
    }

    /**
     * Create
     *
     * Store a newly created subscriber in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $clientIp = $request->ip();

        $subscriber = new Subscriber();
        $subscriber->email = $request->get('email');
        $subscriber->ip_address = $clientIp;
        $subscriber->created_at = Carbon::now();
        $subscriber->save();

        return new JsonResponse(SubscriberResource::make($subscriber));
    }

    /**
     * Show
     *
     * Display the specified subscriber.
     * @authenticated
     * @param Subscriber $subscriber
     * @return JsonResponse
     */
    public function show(Subscriber $subscriber): JsonResponse
    {
        return new JsonResponse(SubscriberResource::make($subscriber));
    }

    /**
     * Edit
     *
     * Update the specified subscriber in storage.
     * @authenticated
     * @param Request $request
     * @param Subscriber $subscriber
     * @return JsonResponse
     */
    public function update(Request $request, Subscriber $subscriber): JsonResponse
    {
        $subscriber->update($request->all());

        return new JsonResponse(SubscriberResource::make($subscriber));
    }

    /**
     * Delete
     *
     * Remove the specified subscriber from storage.
     * @authenticated
     * @param Subscriber $subscriber
     * @return JsonResponse
     */
    public function destroy(Subscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return new JsonResponse([]);
    }
}
