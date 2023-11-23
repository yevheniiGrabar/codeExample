<?php

namespace App\Http\Controllers;

use App\Enums\PickingStatusEnum;
use App\Http\Requests\Picking\IndexRequest;
use App\Http\Requests\Picking\ShowRequest;
use App\Http\Requests\Picking\UpdateRequest;
use App\Http\Resources\PickingResource;
use App\Models\BatchNumber;
use App\Models\Company;
use App\Models\LocationProduct;
use App\Models\PickingBatchNumber;
use App\Models\PickingSerialNumber;
use App\Models\SaleOrder;
use App\Models\SaleOrderLine;
use App\Models\SerialNumber;
use App\Services\JsonResponseDataTransform;
use App\Traits\CurrentCompany;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Picking
 *
 * Endpoints for managing pickings
 */
class PickingController extends Controller
{
    private JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available sale orders
     * @authenticated
     */
    public function index(IndexRequest $request)
    {
        $pickings = SaleOrder::query()
            ->currentCompany()
            ->when($request->has('customer_id'), function ($query) use ($request) {
                return $query->where('customer_id', $request->input('customer_id'));
            })
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->where('preferred_delivery_date', $request->input('date'));
            })
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->input('status'));
            })
            ->with(['customer', 'lines.product.locations'])
            ->orderByDesc('preferred_delivery_date')
            ->get();

        return $this->dataTransform->conditionalResponse(
            $request,
            PickingResource::collection($pickings)
        );
    }

    /**
     * Show
     *
     * Display the specified sale orders
     * @authenticated
     */
    public function show(ShowRequest $request, SaleOrder $picking): JsonResponse
    {
        $picking->load(['customer', 'lines.product.locations']);

        return response()->json([
            'payload' => PickingResource::make($picking)
        ]);
    }

    /**
     * Edit
     *
     * Update the specified sale order
     * @authenticated
     */
    public function update(UpdateRequest $request, SaleOrder $picking): \Illuminate\Http\Response
    {
        if ($picking->company_id != CurrentCompany::getDefaultCompany()?->company_id) {
            throw new AuthorizationException();
        }

        $data = $request->validated();

        $fullyReceived = true;

        foreach ($data['lines'] as $line) {
            $saleOrderLine = SaleOrderLine::find($line['id']);

            foreach ($line['locations'] as $location) {
                $locationProduct = LocationProduct::query()
                    ->where('location_id', $location['location_id'])
                    ->when(isset($location['sub_location_id']), function ($query) use ($location) {
                        return $query->where('sub_location_id', $location['sub_location_id']);
                    })
                    ->where('product_id', $saleOrderLine->product_id)
                    ->first();

                if ($locationProduct) {
                    if (
                        $saleOrderLine->quantity < $location['picked_quantity'] ||
                        $locationProduct->in_stock < $location['picked_quantity']
                    ) {
                        throw new Exception('Bad picking quantity value');
                    }

                    $locationProduct->update([
                        'in_stock' => $locationProduct->in_stock - $location['picked_quantity']
                    ]);
                }

                $saleOrderLine->update([
                    'picked_quantity' => $saleOrderLine->picked_quantity + $location['picked_quantity']
                ]);

                if($saleOrderLine->quantity > $saleOrderLine->picked_quantity) {
                    $fullyReceived = false;
                }
            }

            if(isset($line['serial_numbers'])) {
                foreach ($line['serial_numbers'] as $serialNumber) {
                    SerialNumber::where('product_id', $saleOrderLine->product_id)
                        ->where('serial_number', $serialNumber['serial_number'])
                        ->limit(1)
                        ->delete();

                    PickingSerialNumber::create([
                        'sale_order_id' => $picking->id,
                        'product_id' => $saleOrderLine->product_id,
                        'serial_number' => $serialNumber['serial_number'],
                    ]);
                }
            }

            if(isset($line['batch_numbers'])) {
                foreach ($line['batch_numbers'] as $batchNumber) {
                    BatchNumber::where('product_id', $saleOrderLine->product_id)
                        ->where('batch_number', $batchNumber['batch_number'])
                        ->limit(1)
                        ->delete();

                    PickingBatchNumber::create([
                        'sale_order_id' => $picking->id,
                        'product_id' => $saleOrderLine->product_id,
                        'batch_number' => $batchNumber['batch_number'],
                        'expiration_date' => $batchNumber['expiration_date'] ?? null
                    ]);
                }
            }
        }

        $picking->update([
            'picking_status' => (string)($fullyReceived ? PickingStatusEnum::PICKED : PickingStatusEnum::PARTIALLY_COMPLETED)
        ]);

        $pdf = Pdf::loadView('PDF.delivery-note', [
            'currentCompany' => Company::find(CurrentCompany::getDefaultCompany()->company_id),
            'saleOrder' => $picking,
            'customer' => $picking->customer,
            'totalWeight' => '',
        ]);

        return $pdf->download('SaleOrder.pdf');
    }
}
