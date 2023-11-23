<?php

namespace App\Exports;

use App\Models\CustomerInvoice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerInvoiceExport implements FromCollection,WithHeadings,WithMapping
{
    /**
     * @return Collection
     */

    public function collection(): Collection
    {
        return CustomerInvoice::all();
    }

    public function map($row): array
    {
        return [
            $row->sale_order_id,
            $row->customer_id,
            $row->currency_id,
            $row->total,
            $row->invoice_date,
            $row->status,
            $row->billing_address_id,
            $row->our_reference_id,
            $row->their_reference_id,
            $row->delivery_term_id,
            $row->currency_rate,
            $row->created_at,
            $row->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'sale_order_id',
            'customer_id',
            'currency_id',
            'total',
            'invoice_date',
            'status',
            'billing_address_id',
            'our_reference_id',
            'their_reference_id',
            'delivery_term_id',
            'currency_rate',
            'created_at',
            'updated_at',
        ];
    }
}
