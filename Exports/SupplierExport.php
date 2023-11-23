<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection,WithHeadings,WithMapping
{
    /**
     * @return Collection
     */

    public function collection(): Collection
    {
        return Supplier::all();
    }

    public function map($row): array
    {
        return [
            $row->company_name,
            $row->contact_person,
            $row->phone,
            $row->email,
            $row->supplier_registration_no,
            $row->billing_address_id,
            $row->language_id,
            $row->created_at,
            $row->updated_at,
            $row->delivery_address_id,
            $row->currency_id
        ];
    }

    public function headings(): array
    {
        return [
            'company_name',
            'contact_person',
            'phone',
            'email',
            'supplier_registration_no',
            'billing_address_id',
            'language_id',
            'created_at',
            'updated_at',
            'delivery_address_id',
            'currency_id'
        ];
    }
}
