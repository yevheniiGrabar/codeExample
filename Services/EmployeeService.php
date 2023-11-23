<?php

namespace App\Services;

use App\Models\Employee;
use App\Traits\CurrentCompany;

class EmployeeService
{
    public function createEmployee(array $data): Employee
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();
        $employee = new Employee;

        $employee->employee_number = $data['employee_number'];
        $employee->name = $data['name'];
        $employee->job_title = $data['job_title'] ?? null;
        $employee->email = $data['email'];
        $employee->phone = $data['phone'] ?? null;
        $employee->company_id = $defaultCompany->company_id;
        $employee->language_id = $data['language'] ?? null;

        $employee->save();

        return $employee;
    }

    public function updateEmployee($employee, array $data): Employee
    {
        $employee->update(
            [
                'employee_number' => $data['employee_number'] ?? $employee->employee_number,
                'name' => $data['name'] ?? $employee->name,
                'job_title' => $data['job_title'] ?? $employee->job_title,
                'email' => $data['email'] ?? $employee->email,
                'phone' => $data['phone'] ?? $employee->phone,
                'language_id' => $data['language'] ?? $employee->language_id,
            ]
        );

        return $employee;
    }
}
