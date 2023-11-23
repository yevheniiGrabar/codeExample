<?php

namespace App\Http\Controllers;

use App\Models\SupplierReturn;
use Illuminate\Http\Request;

/**
 * @group Supplier return
 *
 * Endpoints for managing supplier returns
 */
class SupplierReturnController extends Controller
{
    /**
     * List
     *
     * Returns list of available supplier returns
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
     * Store a newly created supplier return in storage.
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
     * Display the specified supplier return.
     * @authenticated
     * @param  \App\Models\SupplierReturn  $supplierReturn
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierReturn $supplierReturn)
    {
        //
    }

    /**
     * Edit
     *
     * Update the specified supplier return in storage.
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierReturn  $supplierReturn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierReturn $supplierReturn)
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified supplier return from storage.
     * @authenticated
     * @param  \App\Models\SupplierReturn  $supplierReturn
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierReturn $supplierReturn)
    {
        //
    }
}
