<?php

namespace App\Services;

use App\Enums\ReceiveStatusEnum;
use App\Models\Company;
use App\Models\DeliveryAddress;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Traits\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PurchaseOrderService
{

    /**
     * @param Request $request
     * @param $purchaseOrder
     * @return Product
     */
    public function addDocuments(Request $request, $purchaseOrder): Product
    {
        if ($request->hasFile('documents')) {
            $purchaseOrder->addMultipleMediaFromRequest(['documents'])
                ->each(
                    function ($fileAdder) {
                        $fileAdder->toMediaCollection('documents');
                    }
                );
        }

        return $purchaseOrder;
    }

    /**
     * @param $purchaseOrder
     * @return PurchaseOrder
     */
    public function loadAdditionalData($purchaseOrder): PurchaseOrder
    {
        $purchaseOrder
            ->loadMissing(
                [
                    'supplier' => fn($q) => $q->select(['id', 'name', 'code']),
                    'currency' => fn($q) => $q->select(['id', 'name', 'code']),
                    'ourReference' => fn($q) => $q->select(['*']),
                    'ourReference.language',
                    'theirReference' => fn($q) => $q->select(['id', 'contact_person as name', 'code']),
                    'paymentTerms' => fn($q) => $q->select(['*']),
                    'deliveryTerms' => fn($q) => $q->select(['*']),
                    'lines' => fn($q) => $q->select(
                        [
                            'id',
                            'purchase_order_id',
                            'tax_id',
                            'product_id',
                            'quantity',
                            'unit_price',
                            'discount',
                            'received_quantity'
                        ]
                    ),
                    'lines.product' => fn($q) => $q->select(
                        ['id', 'name', 'product_code as code', 'has_batch_number', 'has_serial_number']
                    ),
                    'lines.tax' => fn($q) => $q->select(['id', 'rate']),
                    'receives' => fn($q) => $q->select(
                        [
                            'id',
                            'purchase_order_id',
                            'user_id',
                            'receive_date',
                            'is_fully_received',
                        ]
                    ),
                    'receives.user' => fn($q) => $q->select(['id', 'name as first_name', 'last_name', 'email']),
                    'deliveryAddress' => fn($q) => $q->select(['*'])
                ]
            );

        return $purchaseOrder;
    }

    /**
     * @param array $data
     * @return PurchaseOrder
     * @throws ValidationException
     */
    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $company = Company::query()->find($currentCompany->company_id);

        $purchaseOrder = new PurchaseOrder();

        $purchaseOrder->supplier_id = $data['supplier'];
        $purchaseOrder->purchase_date = $data['purchase_date'];
        $purchaseOrder->preferred_delivery_date = $data['preferred_delivery_date'] ?? null;
        $purchaseOrder->our_reference_id = $data['our_reference'] ?? null;
        $purchaseOrder->their_reference_id = $data['their_reference'] ?? null;
        $purchaseOrder->payment_term_id = $data['payment_terms'] ?? null;
        $purchaseOrder->delivery_term_id = $data['delivery_terms'] ?? null;
        $purchaseOrder->currency_id = $data['currency'];
        $purchaseOrder->is_billing_for_delivery = $data['is_billing_for_delivery'];
        $purchaseOrder->company_id = $currentCompany->company_id;
        $purchaseOrder->receive_state = ReceiveStatusEnum::NEW;

        if ($company->company_billing_id != null) {
            $purchaseOrder->billing_address_id = $company->company_billing_id;
        } else {
            throw ValidationException::withMessages(
                [
                    'billing_address' => 'This field is required'
                ]
            );
        }

        $deliveryAddress = DeliveryAddress::query()->where('company_id', $currentCompany->company_id)->first();

        $purchaseOrder->delivery_address_id = $deliveryAddress->id;

        $purchaseOrder->save();


        if (isset($data['orders']) && sizeof($data['orders']) > 0) {
            $this->addOrderLines($data['orders'], $purchaseOrder);
        }

        return $purchaseOrder;
    }

    /**
     * @param array $data
     * @param $purchaseOrder
     * @return PurchaseOrder
     */
    public function updatePurchaseOrder(array $data, $purchaseOrder): PurchaseOrder
    {
        $purchaseOrder->update(
            [
                'supplier_id' => $data['supplier'] ?? $purchaseOrder->supplier_id,
                'purchase_date' => $data['purchase_date'] ?? $purchaseOrder->purchase_date,
                'preferred_delivery_date' => $data['preferred_delivery_date'] ?? $purchaseOrder->preferred_delivery_date,
                'our_reference_id' => $data['our_reference'] ?? $purchaseOrder->our_reference_id,
                'their_reference_id' => $data['their_reference'] ?? $purchaseOrder->their_reference_id,
                'payment_term_id' => $data['payment_term'] ?? $purchaseOrder->payment_term_id,
                'delivery_term_id' => $data['delivery_term'] ?? $purchaseOrder->delivery_term_id,
                'currency_id' => $data['currency'] ?? $purchaseOrder->currency_id,
                'delivery_address_id' => $data['delivery_address'] ?? $purchaseOrder->delivery_address_id,
                'is_billing_for_delivery' => $data['is_billing_for_delivery'] ? 1 : 0,
            ]
        );

        $orderLinesData = [];

        foreach ($data['orders'] as $order) {
            $orderLineData = [
                'id' => $order['id'] ?? null,
                'product_id' => $order['product'],
                'quantity' => $order['quantity'],
                'unit_price' => $order['unit_price'],
                'discount' => $order['discount'],
                'tax_id' => $order['tax'] ?? null,
            ];

            $orderLinesData[] = $orderLineData;

            Product::find($order['product'])->update([
                'cost_price' => $order['unit_price']
            ]);
        }

        // deleting existing OrderLines
        if (isset($data['deleted_orders']) && is_array($data['deleted_orders'])) {
            foreach ($data['deleted_orders'] as $id) {
                OrderLine::destroy($id);
            }
        }

        // updating existing & create a new OrderLines
        foreach ($orderLinesData as $orderLineData) {
            $orderLineId = $orderLineData['id'] ?? null;

            if ($orderLineId) {
                $orderLine = OrderLine::query()->findOrFail($orderLineId);
                $orderLine->update($orderLineData);
            } else {
                $purchaseOrder->lines()->create($orderLineData);
            }
        }
        return $purchaseOrder;
    }

    public function saveDocumentsFromRequest(Request $request)
    {
        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');

            foreach ($documents as $document) {
                $this->addMedia($document)->toMediaCollection('documents');
            }
        }
    }

    /**
     * @return int
     */
    public function countDocuments(): int
    {
        return $this->getMedia('documents')->count();
    }

    /**
     * @param array $orders
     * @param $purchaseOrder
     */
    public function addOrderLines(array $orders, $purchaseOrder)
    {
        foreach ($orders as $order) {
            $orderLine = new OrderLine();
            $orderLine->product_id = $order['product'];
            $orderLine->quantity = $order['quantity'];
            $orderLine->unit_price = $order['unit_price'];
            $orderLine->discount = $order['discount'] ?? null;
            $orderLine->tax_id = $order['tax'] ?? null;
            $orderLine->purchaseOrder()->associate($purchaseOrder);

            $product = Product::query()->find($order['product']);
            $product->update(['cost_price' => $order['unit_price']]);

            $product->save();
            $orderLine->save();

            Product::find($order['product'])->update([
                'cost_price' => $order['unit_price']
            ]);
        }
    }

    /**
     * @return Collection|Builder[]
     */
    public function getPurchaseOrders(): array|Collection
    {
        return PurchaseOrder::query()->with(
            ['lines.product' => fn($q) => $q->select('id'), 'lines' => fn($q) => $q->select(['*'])]
        )->get();
    }
}
