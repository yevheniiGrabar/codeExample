<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Http\Requests\Customer\DestroyRequest;
use App\Http\Requests\Customer\ExportRequest;
use App\Http\Requests\Customer\ShowRequest;
use App\Http\Requests\Supplier\IndexRequest;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\JsonResponseDataTransform;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * @group Supplier
 *
 * Endpoints for managing suppliers
 */
class SupplierController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    private SupplierService $supplierService;

    public function __construct(JsonResponseDataTransform $dataTransform, SupplierService $supplierService)
    {
        $this->dataTransform = $dataTransform;
        $this->supplierService = $supplierService;
    }

    /**
     * List
     *
     * Returns list of available suppliers
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $parsedRequest = $this->supplierService->supplierParseRequest($request);
        $currentCompany = $this->supplierService->getCurrentCompany();

        $suppliers = Supplier::filter($parsedRequest['search'])
            ->with(
                [
                    'contacts' => fn($q) => $q->select(['id', 'supplier_id', 'name', 'phone', 'email'])
                ]
            )
            ->orderBy($parsedRequest['orderBy']['name'] ?? 'id', $parsedRequest['orderBy']['type'] ?? 'desc')
            ->where('company_id', $currentCompany->company_id)
            ->get();

        if ($request->has('slim') && $request->get('slim') != false) {
            return new JsonResponse(
                [
                    'payload' => SupplierResource::setMode('slim')::collection($suppliers)
                ]
            );
        }

        return $this->dataTransform->conditionalResponse(
            $request,
            SupplierResource::collection($suppliers)
        );
    }

    /**
     * Create
     *
     * Store a newly created supplier in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $supplierData = $request->validated();
        $supplier = $this->supplierService->createSupplier($supplierData);


        if (!$supplier instanceof Supplier) {
            return response()->json(['error' => 'Failed to create supplier'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $supplierWithAdditionalData = $this->supplierService->loadAdditionalData($supplier);

        return new JsonResponse(
            [
                'payload' => new SupplierResource($supplierWithAdditionalData)
            ]
        );
    }

    /**
     * Show
     *
     * Display the specified supplier.
     * @authenticated
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->load(
            [
                'country',
                'contacts' => fn($q) => $q->select(['id', 'supplier_id', 'name', 'phone', 'email']),
                'tax' => fn($q) => $q->select(['id', 'rate']),
                'returns' => fn($q) => $q->select(['*']),
                'returns.country' => fn($q) => $q->select('id','name')

            ]
        );

        return new JsonResponse(['payload' => SupplierResource::setMode('details')::make($supplier)]);
    }

    /**
     * Edit
     *
     * Update the specified supplier in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Supplier $supplier
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UpdateRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->updateSupplier($supplier, $request->validated());

        return new JsonResponse(new SupplierResource($supplier));
    }

    /**
     * Delete
     *
     * Remove the specified supplier from storage.
     * @authenticated
     * @param  DestroyRequest  $request
     * @param  Supplier  $supplier
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        return new JsonResponse([]);
    }

    /**
     * Export
     *
     * Export all suppliers from storage to csv(xslt).
     * @authenticated
     * @param  ExportRequest  $request
     * @return BinaryFileResponse
     */
    public function export(ExportRequest $request): BinaryFileResponse
    {
        return Excel::download(new SupplierExport(), 'SupplierExport.xlsx');
    }
}
