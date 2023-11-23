<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Requests\Customer\DestroyRequest;
use App\Http\Requests\Customer\ExportRequest;
use App\Http\Requests\Customer\ImportRequest;
use App\Http\Requests\Customer\IndexRequest;
use App\Http\Requests\Customer\ShowRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerSlimResource;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Customer
 *
 * Endpoints for managing customers
 */
class CustomerController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    private CustomerService $customerService;

    public function __construct(JsonResponseDataTransform $dataTransform, CustomerService $customerService)
    {
        $this->dataTransform = $dataTransform;
        $this->customerService = $customerService;
    }

    /**
     * List
     *
     * Returns list of available customers
     * @authenticated
     * @param  IndexRequest  $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $parsedRequest = $this->customerService->customerParseRequest($request);
        $customers = Customer::filter($parsedRequest['search'])->where('company_id',
            $currentCompany->company_id)->orderBy('id', 'desc')->get();

        if ($request->has('slim') && $request->get('slim')) {
            return new JsonResponse(
                ['payload' => CustomerSlimResource::collection($customers)],
                Response::HTTP_OK
            );
        }

        return $this->dataTransform->conditionalResponse($request, CustomerResource::collection($customers));
    }

    /**
     * Create
     * Store a newly created customer in storage.
     * @authenticated
     * @param  StoreRequest  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());
        return new JsonResponse(
            [
                'payload' => CustomerResource::make($customer)
            ], Response::HTTP_CREATED
        );
    }

    /**
     * Show
     *
     * Display the specified customer.
     * @authenticated
     * @param  ShowRequest  $request
     * @param  Customer  $customer
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Customer $customer): JsonResponse
    {
        $customer->loadMissing(
            [
                'billingAddress',
                'billingAddress.country' => fn($q) => $q->select('id', 'name'),
                'deliveryAddresses',
                'deliveryAddresses.country'
            ]
        );

        return new JsonResponse(['payload' => CustomerResource::make($customer)], Response::HTTP_OK);
    }

    /**
     * Edit
     *
     * Update the specified customer in storage.
     * @authenticated
     * @param  UpdateRequest  $request
     * @param  Customer  $customer
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->updateCustomerData($customer, $request->validated());

        return new JsonResponse(['payload' => CustomerResource::make($customer)], Response::HTTP_OK);
    }

    /**
     * Delete
     *
     * Remove the specified customer from storage.
     * @authenticated
     * @param  DestroyRequest  $request
     * @param  Customer  $customer
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Customer $customer): JsonResponse
    {
        $customer->delete();

        return new JsonResponse(['message' => ' Customer Deleted  successfully'], Response::HTTP_OK);
    }

    /**
     * Import
     *
     * Import all customers from csv(xslt) to storage.
     * @authenticated
     * @param  ImportRequest  $request
     * @return JsonResponse
     */
    public function import(ImportRequest $request): JsonResponse
    {
        $import = new CustomerImport($request->input('mapping'));
        Excel::import($import, $request->file('import'));

        return new JsonResponse(['message' => 'Products imported successfully']);
    }

    /**
     * Export
     *
     * Export all customers from storage to csv(xslt).
     * @authenticated
     * @param  ExportRequest  $request
     * @return BinaryFileResponse
     */
    public function export(ExportRequest $request): BinaryFileResponse
    {
        $selected = $request->input('columns');

        return Excel::download(new CustomerExport($selected), 'CustomerExport.xlsx');
    }
}
