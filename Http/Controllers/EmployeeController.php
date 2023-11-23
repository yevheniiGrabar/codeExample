<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\DestroyRequest;
use App\Http\Requests\Employee\IndexRequest;
use App\Http\Requests\Employee\ShowRequest;
use App\Http\Requests\Employee\StoreRequest;
use App\Http\Requests\Employee\UpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Employee
 *
 * Endpoints for managing employees
 */
class EmployeeController extends Controller
{
    protected JsonResponseDataTransform $dataTransform;
    protected EmployeeService $employeeService;

    public function __construct(JsonResponseDataTransform $dataTransform, EmployeeService $employeeService)
    {
        $this->dataTransform = $dataTransform;
        $this->employeeService = $employeeService;
    }

    /**
     * List
     * 
     * Returns list of selected company employees.
     * @authenticated
     * @param Company $company
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Company $company, IndexRequest $request): JsonResponse
    {
        $employees = $company->employees;

        return $this->dataTransform->conditionalResponse(
            $request,
            EmployeeResource::collection($employees)
        );
    }

    /**
     * Create
     * 
     * Store a new created employee in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->validated());

        return new JsonResponse(new EmployeeResource($employee), Response::HTTP_CREATED);
    }

    /**
     * Show
     * 
     * Display the specified employee.
     * @authenticated
     * @param Employee $employee
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Employee $employee): JsonResponse
    {
        return new JsonResponse(EmployeeResource::make($employee));
    }

    /**
     * Edit
     * 
     * Update the specified employee in storage.
     * @authenticated
     * @param UpdateRequest $request
     * @param Employee $employee
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Employee $employee): JsonResponse
    {
        $employeeData = $this->employeeService->updateEmployee($employee, $request->all());

        return new JsonResponse(EmployeeResource::make($employeeData), Response::HTTP_OK);
    }

    /**
     * Delete
     * 
     * Remove the specified employee from storage.
     * @authenticated
     * @param Employee $employee
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Employee $employee): JsonResponse
    {
        $employee->delete();

        return new JsonResponse(['message' => 'Employee deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
