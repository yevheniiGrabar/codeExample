<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditNoteRequest;
use App\Http\Resources\CreditNoteResource;
use App\Models\CreditNote;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Credit Note
 *
 * Endpoints for managing credit notes
 */
class CreditNoteController extends Controller
{
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available credit notes
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            CreditNoteResource::collection(
                CreditNote::query()->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created credit note in storage.
     * @authenticated
     * @param CreditNoteRequest $request
     * @return CreditNoteResource
     */
    public function store(CreditNoteRequest $request): CreditNoteResource
    {
        $creditNote = CreditNote::query()->create($request->validated());

        return new CreditNoteResource($creditNote);
    }

    /**
     * Show
     *
     * Display the specified credit note.
     * @authenticated
     * @param CreditNote $creditNote
     * @return CreditNoteResource
     */
    public function show(CreditNote $creditNote): CreditNoteResource
    {
        return CreditNoteResource::make($creditNote);
    }

    /**
     * Edit
     *
     * Update the specified credit note in storage.
     * @authenticated
     * @param CreditNoteRequest $request
     * @param CreditNote $creditNote
     * @return CreditNoteResource
     */
    public function update(CreditNoteRequest $request, CreditNote $creditNote): CreditNoteResource
    {
        $creditNote->update($request->all());

        return new CreditNoteResource($creditNote);
    }

    /**
     * Delete
     *
     * Remove the specified credit note from storage.
     * @authenticated
     * @param CreditNote $creditNote
     * @return JsonResponse
     */
    public function destroy(CreditNote $creditNote): JsonResponse
    {
        $creditNote->delete();

        return new JsonResponse([]);
    }
}
