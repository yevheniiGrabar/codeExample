<?php

namespace App\Http\Controllers;

use App\Support\Ability\AbilityResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Role
 *
 * Endpoints for managing companies
 */
class PermissionController extends Controller
{
    /**
     * List.
     *
     * Returns list of modules with their permissions.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'payload' => AbilityResolver::abilityGroups()
        ]);
    }
}
