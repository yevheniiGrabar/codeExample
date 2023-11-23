<?php

namespace App\Imports;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PurchaseOrdersImport implements ToModel,WithHeadingRow
{
    /**
     * @param array $row
     * @return array|Model|Builder|null
     */

    public function model(array $row): array|Model|Builder|null
    {
        return PurchaseOrder::query()->create(
            [
                'billing_address_id' => $row['billing_address_id'] ?? null,
                'delivery_term_id' => $row['delivery_term_id'] ?? null,
                'payment_term_id' => $row['payment_term_id'] ?? null,
                'approved_by' => $row['approved_by'] ?? null,
                'our_reference' => $row['our_reference'] ?? null,
                'their_reference' => $row['their_reference'] ?? null,
                'other_reference' => $row['other_reference'] ?? null,
                'currency_id' => $row['currency_id'] ?? null,
                'is_accepted' => $row['is_accepted'] ?? 'is_booked' === 0,
                'is_approved' => $row['is_approved'] ?? 'is_booked' === 0,
                'is_booked' => $row['is_booked'] ?? 'is_booked' === 0,
                'is_cancelled' => $row['is_cancelled'] ?? 'is_cancelled' === 0,
                'is_invoiced' => $row['is_invoiced'] ?? 'is_invoiced' === 0,
                'is_on_hold' => $row['is_on_hold'] ?? 'is_on_hold' === 0,
                'is_partly_invoiced' => $row['is_partly_invoiced'] ?? 'is_partly_invoiced' === 0,
                'is_partly_received' => $row['is_partly_received'] ?? 'is_partly_received' === 0,
                'is_ready_for_invoicing' => $row['is_ready_for_invoicing'] ?? 'is_ready_for_invoicing' === 0,
                'is_ready_for_receiving' => $row['is_ready_for_receiving'] ?? 'is_ready_for_receiving' === 0,
                'is_received' => $row['is_received'] ?? 'is_received' === 0,
                'is_rejected' => $row['is_rejected'] ?? 'is_rejected' === 0,
                'is_sent' => $row['is_sent'] ?? 'is_sent' === 0,
                'quantity' => $row['quantity'] ?? null,
                'preferred_delivery_date' => $row['preferred_delivery_date'] ?? null,
                'purchase_date' => $row['purchase_date'] ?? null,
                'total' => $row['total'] ?? null,
                'currency_rate' => $row['currency_rate'] ?? null,
                'received_at' => $row['received_at'] ?? null,
                'created_at' => $row['created_at'] ?? null,
                'updated_at' => $row['updated_at'] ?? null,
                'delivery_address_id' => $row['delivery_address_id'] ?? null,
                'supplier_id' => $row['supplier_id'] ?? null,
                'receives_id' => $row['receives_id'] ?? null,
                'supplier_invoice_id' => $row['supplier_invoice_id'] ?? null,
            ]
        );
    }
}
