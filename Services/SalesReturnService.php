<?php

namespace App\Services;

use App\Models\SalesReturn;
use Illuminate\Database\Eloquent\Collection;

class SalesReturnService
{
    /**
     * @return Collection|array
     */
    public function getSalesReturnData(): Collection|array
    {
        return SalesReturn::query()
//            ->join('shipments', 'shipments.id', '=','sales_returns.shipment_id')
//            ->join('sale_orders', 'sale_order_id', '=', 'shipments.id')
//            ->join('sale_order_lines', 'sale_orders.id', '=', 'sale_order_lines.sale_order_id')
            ->with(
                [
                    'customer' => fn($q) => $q->select(['id', 'customer_name']),
                    'shipment' => fn($q) => $q->select(['id', 'sale_order_id']),
                    'shipment.saleOrder' => fn($q) => $q->select(['id']),
                    'shipment.saleOrder.saleOrderLines' => fn($q) => $q->select(['id', 'product_id']),
                    'shipment.saleOrder.SaleOrderLines.product' => fn($q) => $q->select(['id', 'name', 'cost_price']),
                    'shipment.saleOrder.saleOrderLines.product.locations' => fn($q) => $q->select(
                        ['id', 'name', 'product_id']
                    ),
                    'shipment.saleOrder.saleOrderLines.product.locations.sections' => fn($q) => $q->select(
                        ['id', 'section_name']
                    ),
                ]
            )
            ->orderBy('id','desc')
            ->get();
    }
}
