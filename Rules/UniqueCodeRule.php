<?php

namespace App\Rules;

use App\Traits\CurrentCompany;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueCodeRule implements Rule
{
    protected string $table_name;
    protected string $column_name;

    /**
     * Create a new rule instance.
     *
     * @param $table_name
     * @param $column_name
     */
    public function __construct($table_name, $column_name)
    {
        $this->table_name = $table_name;
        $this->column_name = $column_name;
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
        $currentCompany = CurrentCompany::getDefaultCompany();
        $existingRecord = DB::table($this->table_name)
            ->where('company_id', $currentCompany->company_id)
            ->where($this->column_name, $value)
            ->first();

        // If product with company_id & code exist we return false
        if ($existingRecord) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A  ' . $this->table_name . ' with this code already exists for this company.';
    }
}
