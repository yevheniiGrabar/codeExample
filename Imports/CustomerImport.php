<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CustomerImport implements ToCollection
{
    protected mixed $mapping;

    public function __construct($mapping = null)
    {
        $this->mapping = $mapping;
    }

    public function collection(Collection $collection): void
    {
        foreach ($collection as $coll) {
            $customer = new Customer();

            if ($this->mapping) {
                $mapping = json_decode($this->mapping, true);
                foreach ($mapping as $col => $field) {
                    $column = $this->getField($col);
                    $value = $coll[$column];
                    $customer->$field = $value;
                }
            }
            $customer->save();
        }
    }

    private function getField($col): array|string
    {
        return str_replace(' ', '_', strtolower($col));
    }
}
