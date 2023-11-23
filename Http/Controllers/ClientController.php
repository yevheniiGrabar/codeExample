<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Client
 *
 * Endpoints for managing clients
 */
class ClientController extends Controller
{
    /**
     * List
     *
     * Returns list of available clients
     * @authenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $clients = Client::query()
            ->where('company_id', '=', $currentCompany->company_id)
            ->paginate(5);

        return new JsonResponse(ClientResource::collection($clients));
    }

    /**
     * Create
     *
     * Store a newly created client in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $client = Client::query()->create($request->all());

        return new JsonResponse(new ClientResource($client));
    }

    /**
     * Show
     *
     * Display the specified client.
     * @authenticated
     * @param Client $client
     * @return JsonResponse
     */
    public function show(Client $client): JsonResponse
    {
        return new JsonResponse(ClientResource::make($client));
    }

    /**
     * Edit
     *
     * Update the specified client in storage.
     * @authenticated
     * @param Request $request
     * @param Client $client
     * @return JsonResponse
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $client->update($request->all());

        return new JsonResponse(ClientResource::collection($client));
    }

    /**
     * Delete
     *
     * Remove the specified client from storage.
     * @authenticated
     * @param Client $client
     * @return JsonResponse
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return new JsonResponse([]);
    }
}
