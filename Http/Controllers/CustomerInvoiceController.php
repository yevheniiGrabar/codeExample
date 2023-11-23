<?php

namespace App\Http\Controllers;

use App\Exports\CustomerInvoiceExport;
use App\Models\CustomerInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Customer invoice
 *
 * Endpoints for managing customer invoices
 */
class CustomerInvoiceController extends Controller
{
    /**
     * List
     *
     * Returns list of available customer invoices
     * @authenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $customerInvoices = CustomerInvoice::query()->paginate(5);

        return new JsonResponse($customerInvoices);
    }

    /**
     * Create
     *
     * Store a newly created customer invoice in storage.
     * @authenticated
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        //
    }

    /**
     * Show
     *
     * Display the specified customer invoice.
     * @authenticated
     * @param CustomerInvoice $customerInvoice
     * @return Response
     */
    public function show(CustomerInvoice $customerInvoice): Response
    {
        //
    }

    /**
     * Edit
     *
     * Update the specified customer invoice in storage.
     * @authenticated
     * @param Request $request
     * @param CustomerInvoice $customerInvoice
     * @return Response
     */
    public function update(Request $request, CustomerInvoice $customerInvoice): Response
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified customer invoice from storage.
     * @authenticated
     * @param CustomerInvoice $customerInvoice
     * @return Response
     */
    public function destroy(CustomerInvoice $customerInvoice): Response
    {
        //
    }

    /**
     * Export
     *
     * Export all customer invoices from storage to csv(xslt).
     * @authenticated
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new CustomerInvoiceExport(), 'CustomerInvoiceExport.xlsx');
    }
}
