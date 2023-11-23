<?php

namespace App\Services;

use App\Enums\ReceiveStatusEnum;
use App\Models\BatchNumber;
use App\Models\LocationProduct;
use App\Models\OrderLine;
use App\Models\PickingBatchNumber;
use App\Models\PickingSerialNumber;
use App\Models\PurchaseOrder;
use App\Models\Receive;
use App\Models\ReceiveBatchNumber;
use App\Models\ReceiveHistory;
use App\Models\ReceiveSerialNumber;
use App\Models\SerialNumber;
use App\Models\Supplier;
use App\Traits\CurrentCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiveService
{
    public function getAdditionalData($request): Collection|\Illuminate\Support\Collection|array
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $filters = json_decode($request->input('filters'), true);

        $receiveList = Receive::query()
            ->when(isset($filters['date']), function ($query) use ($filters) {
                return $query->where('receive_date', 'LIKE', $filters['date'] . '%');
            })
            ->with(
                [
                    'supplier',
                    'user',
                    'receiveHistory' => function ($q) {
                        $q->with(['location','subLocation','product','lines',]);
                    },
                    'purchaseOrder.lines',
                ]
            )
            ->whereHas('supplier', function ($q) use ($filters) {
                if(isset($filters['suppliers'])) {
                    $q->whereIn('id', $filters['suppliers']);
                }
            })
            ->where('company_id', $currentCompany->company_id)
            ->orderByDesc('id')
            ->get();

        $receiveIds = $receiveList->pluck('id')->toArray();

        $receivedQuantities = ReceiveHistory::whereIn('receive_id', $receiveIds)
            ->select('receive_id', DB::raw('COUNT(*) as count'))
            ->groupBy('receive_id')
            ->get()
            ->keyBy('receive_id')
            ->map->count;

        $receiveList->each(
            function ($receive) use ($receivedQuantities) {
                $receive->in_stock = $receivedQuantities->get($receive->id, 0);

                $receive->purchaseOrder->lines->each(
                    function ($orderLine) use ($receive) {
                        if ($receive->receiveHistory->contains('product_id', $orderLine->product_id)) {
                            $orderLine->total_quantity = $orderLine->quantity;
                        } else {
                            $orderLine->total_quantity = 0;
                        }
                    }
                );

                $receive->receiveHistory->each(
                    function ($receiveHistory) use ($receivedQuantities) {
                        $receiveHistory->lines->each(
                            function ($line) use ($receivedQuantities) {
                                $line->in_stock = $receivedQuantities->get($line->product_id, 0);
                            }
                        );
                    }
                );
            }
        );

        return $receiveList;
    }

    public function prepareData(Request $request)
    {
        $supplier = $request->get('supplier');
        $purchaseOrders = $request->get('purchase_order');

        $orders = [];
        foreach ($purchaseOrders as $purchaseOrder) {
            $orders = OrderLine::query()->where('purchase_order_id', $purchaseOrder->id)->get();
        }
    }

    /**
     * @param array $data
     * @return Receive
     */
    public function createNewRecord(array $data): Receive
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $receive = new Receive();
        $receive->supplier_id = $data['supplier'];
        $receive->receive_date = $data['date'];
        $receive->purchase_order_id = $data['purchase_order_id'];
        $receive->company_id = $currentCompany->company_id;
        $receive->user_id = Auth::id();
        $receive->save();

        if (isset($data['receives'])) {
            $this->createReceiveHistoryLines($data['receives'], $receive);
        }

        $purchaseOrder = PurchaseOrder::find($data['purchase_order_id']);

        $status = ReceiveStatusEnum::RECEIVED;

        foreach($purchaseOrder->lines as $line) {
            if($line->quantity != $line->received_quantity) {
                $status = ReceiveStatusEnum::PARTIALLY_COMPLETED;
            }
        }

        $purchaseOrder->update([
            'receive_state' => $status
        ]);

        return $receive;
    }

    /**
     * @param array $data
     * @param $receive
     */
    public function createReceiveHistoryLines(array $data, Receive $receive)
    {
        foreach($data as $receiveLine) {
            $history = $receive->receiveHistory()->create([
                'order_line_id' => $receiveLine['product_order_id'],
                'product_id' => OrderLine::query()->find($receiveLine['product_order_id'])->product_id,
                'location_id' => $receiveLine['location']['store'],
                'sub_location_id' => $receiveLine['location']['section'] ?? null,
                'received_quantity' => $receiveLine['received_quantity']
            ]);

            OrderLine::find($receiveLine['product_order_id'])->update([
                'received_quantity' => $receiveLine['received_quantity']
            ]);

            $locationProduct = LocationProduct::query()
                ->where('company_id', CurrentCompany::getDefaultCompany()->company_id)
                ->where('product_id', $history->product_id)
                ->where('location_id', $history->location_id)
                ->where('sub_location_id', $history->sub_location_id)
                ->first();

            if($locationProduct) {
                $locationProduct->update([
                    'in_stock' => $locationProduct->in_stock + $receiveLine['received_quantity']
                ]);
            } else {
                $locationProduct = LocationProduct::create([
                    'company_id' => CurrentCompany::getDefaultCompany()->company_id,
                    'location_id' => $history->location_id,
                    'product_id' => $history->product_id,
                    'sub_location_id' => $history->sub_location_id,
                    'in_stock' => $receiveLine['received_quantity']
                ]);
            }

            if(isset($receiveLine['serial_numbers'])) {
                foreach ($receiveLine['serial_numbers'] as $serialNumber) {
                    ReceiveSerialNumber::create([
                        'receive_id' => $receive->id,
                        'product_id' => $history->product_id,
                        'serial_number' => $serialNumber['serial_number'],
                    ]);

                    SerialNumber::create([
                        'location_product_id' => $locationProduct->id,
                        'product_id' => $history->product_id,
                        'serial_number' => $serialNumber['serial_number']
                    ]);
                }
            }

            if(isset($receiveLine['batch_numbers'])) {
                foreach ($receiveLine['batch_numbers'] as $batchNumber) {
                    ReceiveBatchNumber::create([
                        'receive_id' => $receive->id,
                        'product_id' => $history->product_id,
                        'batch_number' => $batchNumber['batch_number'],
                        'expiration_date' => $batchNumber['expiration_date']
                    ]);

                    BatchNumber::create([
                        'location_product_id' => $locationProduct->id,
                        'product_id' => $history->product_id,
                        'batch_number' => $batchNumber['batch_number'],
                        'expiration_date' => $batchNumber['expiration_date']
                    ]);
                }
            }
        }
    }
}
