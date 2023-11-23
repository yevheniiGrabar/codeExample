<?php

namespace App\Http\Controllers;

use App\Models\CustomerContacts;
use Illuminate\Http\Request;

/**
 * @group Customer contact
 *
 * Endpoints for managing customer contacts
 */
class CustomerContactsController extends Controller
{
    /**
     * List
     *
     * Returns list of available customer contacts
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
     * Store a newly created customer contact in storage.
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
     * Display the specified customer contact.
     * @authenticated
     * @param  \App\Models\CustomerContacts  $customerContacts
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerContacts $customerContacts)
    {
        //
    }

    /**
     * Edit
     *
     * Update the specified customer contact in storage.
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerContacts  $customerContacts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerContacts $customerContacts)
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified customer contact from storage.
     * @authenticated
     * @param  \App\Models\CustomerContacts  $customerContacts
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerContacts $customerContacts)
    {
        //
    }
}
