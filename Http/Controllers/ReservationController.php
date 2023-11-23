<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

/**
 * @group Reservation
 *
 * Endpoints for managing reservations
 */
class ReservationController extends Controller
{
    /**
     * List
     *
     * Returns list of available  reservations
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
     * Store a newly created  reservation in storage.
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
     * Display the specified  reservation.
     * @authenticated
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Edit
     *
     * Update the specified  reservation in storage.
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Delete
     *
     * Remove the specified  reservation from storage.
     * @authenticated
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
