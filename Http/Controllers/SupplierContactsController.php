<?php

namespace App\Http\Controllers;

use App\Models\SupplierContacts;
use Illuminate\Http\Request;

/**
 * @group Supplier contract
 *
 * Endpoints for managing supplier contracts
 */
class SupplierContactsController extends Controller
{
    /**
     * List
     *
     * Returns list of available supplier contracts
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Create
     *
     * Store a newly created supplier contract in storage.
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show
     *
     * Display the specified supplier contract.
     * @authenticated
     * @param  \App\Models\SupplierContacts  $supplierContacts
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierContacts $supplierContacts)
    {
        //
    }

    /**
     * Edit
     *
     * Update the specified supplier contract in storage.
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierContacts  $supplierContacts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierContacts $supplierContacts)
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified supplier contract from storage.
     * @authenticated
     * @param  \App\Models\SupplierContacts  $supplierContacts
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierContacts $supplierContacts)
    {
        //
    }
}
