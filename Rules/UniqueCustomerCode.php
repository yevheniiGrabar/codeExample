<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueCustomerCode implements Rule
{

    protected int $company_id;
    /**
     * Create a new rule instance.
     *
     * @param $company_id
     */
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $existingCustomerCode = DB::table('customers')
            ->where('company_id', $this->company_id)
            ->where('customer_code', $value)
            ->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'A customer with this code already exists for this company.';
    }
}
