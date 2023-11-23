<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\DestroyRequest;
use App\Http\Requests\Role\IndexRequest;
use App\Http\Requests\Role\ShowRequest;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\JsonResponseDataTransform;
use App\Services\RoleService;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Role
 *
 * Endpoints for managing roles
 */
class RoleController extends Controller
{
    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    /** @var RoleService */
    protected RoleService $roleService;

    public function __construct(JsonResponseDataTransform $dataTransform, RoleService $roleService)
    {
        $this->dataTransform = $dataTransform;
        $this->roleService = $roleService;
    }

    /**
     * List.
     *
     * Returns list of all Roles.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $companies = Role::where('company_id', CurrentCompany::getDefaultCompany()->company_id)->get();

        return $this->dataTransform->conditionalResponse(
            $request,
            RoleResource::collection($companies)
        );
    }

    /**
     * Create.
     *
     * Store new Role.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        return new JsonResponse(['payload' => RoleResource::make($role)], Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified Role.
     * @authenticated
     * @param Role $role
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Role $role): JsonResponse
    {
        return new JsonResponse(['payload' => RoleResource::make($role)], Response::HTTP_OK);
    }

    /**
     * Update
     *
     * Update the specified Role in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Role $role
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->updateRole($role, $request->validated());

        return new JsonResponse(['payload' => RoleResource::make($role)], Response::HTTP_OK);
    }

    /**
     * Delete
     *
     * Remove the specified Role from storage.
     * @authenticated
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Role $role): JsonResponse
    {
        $role->delete();

        return new JsonResponse(['message' => 'Role deleted successfully'], Response::HTTP_OK);
    }
}
