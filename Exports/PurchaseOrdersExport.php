<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseOrdersExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return PurchaseOrder::all();
    }

    public function map($row): array
    {
        return [
            $row->billing_address_id,
            $row->delivery_term_id,
            $row->payment_term_id,
            $row->approved_by,
            $row->our_reference,
            $row->their_reference,
            $row->other_reference,
            $row->currency_id,
            $row->is_accepted,
            $row->is_approved,
            $row->is_booked,
            $row->is_cancelled,
            $row->is_invoiced,
            $row->is_on_hold,
            $row->is_partly_invoiced,
            $row->is_partly_received,
            $row->is_ready_for_invoicing,
            $row->is_ready_for_receiving,
            $row->is_received,
            $row->is_rejected,
            $row->is_sent,
            $row->quantity,
            $row->preferred_delivery_date,
            $row->purchase_date,
            $row->total,
            $row->currency_rate,
            $row->received_at,
            $row->created_at,
            $row->updated_at,
            $row->delivery_address_id,
            $row->supplier_id,
            $row->receives_id,
            $row->supplier_invoice_id,
        ];
    }

    public function headings(): array
    {
        return [
            'billing_address_id',
            'delivery_term_id',
            'payment_term_id',
            'approved_by',
            'our_reference',
            'their_reference',
            'other_reference',
            'currency_id',
            'is_accepted',
            'is_approved',
            'is_booked',
            'is_cancelled',
            'is_invoiced',
            'is_on_hold',
            'is_partly_invoiced',
            'is_partly_received',
            'is_ready_for_invoicing',
            'is_ready_for_receiving',
            'is_received',
            'is_rejected',
            'is_sent',
            'quantity',
            'preferred_delivery_date',
            'purchase_date',
            'total',
            'currency_rate',
            'received_at',
            'created_at',
            'updated_at',
            'delivery_address_id',
            'supplier_id',
            'receives_id',
            'supplier_invoice_id',
        ];
    }
}
