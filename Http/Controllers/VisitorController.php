<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Visitor
 *
 * Endpoints for managing visitors
 */
class VisitorController extends Controller
{
    /**
     * Create
     *
     * Store a newly created visitor return in storage.
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $visitor = new Visitor();
        $visitor->name = $request->get('name');
        $visitor->created_at = Carbon::now();
        $visitor->save();

        return new JsonResponse($visitor);
    }
}
