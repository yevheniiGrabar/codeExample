<?php

namespace App\Exports;

use App\Models\SaleOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SaleOrderExport implements FromCollection,WithHeadings,WithMapping
{
    /**
     * @return Collection
     */

    public function collection(): Collection
    {
        return SaleOrder::all();
    }

    public function map($row): array
    {
        return [
            $row->customer_id,
            $row->delivery_date,
            $row->order_date,
            $row->our_reference_id,
            $row->currency_id,
            $row->currency_rate,
            $row->payment_term_id,
            $row->delivery_term_id,
            $row->billing_address_id,
            $row->delivery_address_id,
            $row->is_sent,
            $row->is_cancelled,
            $row->is_done,
            $row->is_shipped,
            $row->is_ready_for_shipping,
            $row->is_ready_for_invoicing,
            $row->is_invoiced,
            $row->total,
            $row->created_at,
            $row->updated_at,
            $row->receives_id
        ];
    }

    public function headings(): array
    {
        return [
            'customer_id',
            'delivery_date',
            'order_date',
            'our_reference_id',
            'currency_id',
            'currency_rate',
            'payment_term_id',
            'delivery_term_id',
            'billing_address_id',
            'delivery_address_id',
            'is_sent',
            'is_cancelled',
            'is_done',
            'is_shipped',
            'is_ready_for_shipping',
            'is_ready_for_invoicing',
            'is_invoiced',
            'total',
            'created_at',
            'updated_at',
            'receives_id'
        ];
    }
}
