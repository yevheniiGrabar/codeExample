<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

class InvoiceService
{
    /** @var PurchaseOrderService */
    public PurchaseOrderService $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * @return Collection|array
     * @noinspection PhpUnused
     */
    public function saveInvoiceLines(): Collection|array
    {
        $purchaseOrders = $this->purchaseOrderService->getPurchaseOrders();

        foreach ($purchaseOrders as $purchaseOrder) {
            dd($purchaseOrder->lines()->get());
        }

        return $purchaseOrders;
    }
}
